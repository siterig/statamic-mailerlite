<?php

namespace SiteRig\MailerLite;

use Illuminate\Support\Facades\Log;
use MailerLite\MailerLite as MailerLiteApi;
use MailerLite\Exceptions\MailerLiteHttpException as MailerLiteApiHttpException;
use Statamic\Facades\Blueprint;
use Statamic\Support\Arr;

class MailerLite
{
    private $mailerlite = null;
    private $subscriber_data = [];
    private $last_name_field_exists = false;

    public function __construct()
    {
        if ($api_key = config('mailerlite.api_key')) {

            // Connect to mailerlite
            $this->mailerlite = new MailerLiteApi(['api_key' => $api_key]);

        }
    }

    /**
     * Get Subscriber Groups from MailerLite
     *
     * @param $group_id int
     *
     * @return array
     */
    public function getSubscriberGroups(int $group_id = null)
    {
        // Connect to Groups API
        $groups_api = $this->mailerlite->groups;

        // Check if this is a request for a single group
        if ($group_id) {

            // Try catch exception on groups api request
            try {

                // Get single group
                $group_response = $groups_api->find($group_id);
                $group = $group_response['body']['data'];

                // Add group to array
                $subscriber_groups = [
                    'id' => $group['id'],
                    'title' => $group['name'],
                ];

            } catch (MailerLiteApiHttpException $exception) {

                // Add error message
                $subscriber_groups = [
                    'id' => $group_id,
                    'title' => 'Error: group no longer exists',
                ];

            }

        } else {

            // Try catch exception on connection request
            try {

                // Get all groups
                $all_groups = $groups_api->get();

                // Create new array for groups
                $subscriber_groups = [];

                // Loop through groups and put into new array
                foreach ($all_groups['body']['data'] as &$group) {

                    // Add group to array
                    $subscriber_groups[] = [
                        'id' => $group['id'],
                        'title' => $group['name'],
                    ];

                }

            } catch (MailerLiteApiHttpException $exception) {

                // Add error message
                $subscriber_groups = [
                    'id' => $group_id,
                    'title' => 'Error: could not retrieve groups',
                ];

            }

        }

        // Return the array
        return $subscriber_groups;
    }

    /**
     * Get Subscriber Fields from MailerLite
     *
     * @return array
     */
    public function getSubscriberFields(int $field_id = null)
    {
        // Get the all subscriber fields
        $fields_api = $this->mailerlite->fields;
        $all_fields = $fields_api->get();

        // Create new array for fields
        $subscriber_fields = [];

        // Loop through fields and put into new array
        foreach ($all_fields['body']['data'] as &$field) {

            // Check this isn't the name, email or marketing_permissions field
            if (!($field['key'] == 'name')) {

                // Add field to array
                $subscriber_fields[] = [
                    'id' => $field['key'],
                    'title' => $field['name']
                ];

            }

        }

        // Extract the field names to a separate array for sorting
        $field_names = array_column($subscriber_fields, 'title');

        // Sort the array of field names
        array_multisort($field_names, SORT_ASC, $subscriber_fields);

        // Re-index the array to maintain the relationship between id and title
        $subscriber_fields = array_values($subscriber_fields);

        // Return the array
        return $subscriber_fields;
    }

    /**
     * Add Subscriber to MailerLite
     *
     * @param array $config
     * @param object $submission_data
     * @return array
     */
    public function addSubscriber(array $config, object $submission_data)
    {
        // Skip processing if $config is empty or marketing opt-in is not accepted
        if (empty($config) || !$this->checkMarketingOptin($config, $submission_data)) {
            return ['submission' => $submission_data];
        }

        // Initialise subscriber data
        $this->subscriber_data['email'] = $submission_data->get($config['email_field']);

        // Map name if name_field is configured
        if (!empty($config['name_field'])) {
            $this->doMapFields('name', $config['name_field'], $submission_data->toArray(), ' ');
        }

        // Map additional fields
        $this->mapAdditionalFields($config, $submission_data);

        // Automatically split name if enabled and last_name is not mapped
        if (Arr::get($config, 'auto_split_name', true) && !$this->last_name_field_exists) {
            $this->splitName();
        }

        // Add subscriber via MailerLite API
        $response = $this->mailerlite->subscribers->create($this->subscriber_data);

        // Handle API response
        $this->handleApiResponse($response, $config);

        return ['submission' => $submission_data];
    }

    /**
     * Map additional fields from the configuration.
     *
     * @param array $config
     * @param object $submission_data
     */
    protected function mapAdditionalFields(array $config, object $submission_data)
    {
        $mapped_fields = Arr::get($config, 'mapped_fields', []);
        collect($mapped_fields)->each(function ($item) use ($submission_data) {
            if (!empty($item['mapped_form_fields'])) {
                if ($item['subscriber_field'] === 'last_name') {
                    $this->last_name_field_exists = true;
                }
                $this->doMapFields($item['subscriber_field'], $item['mapped_form_fields'], $submission_data->toArray());
            }
        });
    }

    /**
     * Split the name into first and last names if applicable.
     */
    protected function splitName()
    {
        $name = $this->subscriber_data['fields']['name'] ?? '';
        [$first_name, $last_name] = explode(' ', $name, 2) + ['', ''];
        $this->subscriber_data['fields']['name'] = $first_name;
        $this->subscriber_data['fields']['last_name'] = $last_name;
    }

    /**
     * Handle the API response from MailerLite.
     *
     * @param array $response
     * @param array $config
     */
    protected function handleApiResponse(array $response, array $config)
    {
        if (in_array($response['status_code'], [200, 201])) {
            // Add subscriber to group if configured
            if (!empty($config['subscriber_group'])) {
                $this->mailerlite->groups->assignSubscriber(
                    $config['subscriber_group'],
                    $response['body']['data']['id']
                );
            }
        } else {
            \Log::error("MailerLite - {$response['message']} (" . json_encode($response['errors']) . ")");
        }
    }

    /**
     * Are there any Marketing Opt-in fields setup and have they been accepted?
     *
     * @param $config array
     * @param $submission array
     *
     * @return bool
     */
    private function checkMarketingOptin(array $config, object $submission_data)
    {
        // Get marketing opt-in field
        $marketing_optin = Arr::get($config, 'marketing_optin_field', false);

        // Check if marketing permission field is in submission (which indicates it's checked) or if it's not in use
        if ($submission_data->has($marketing_optin) || !($marketing_optin)) {
            return true;
        }

        // Return false as field is setup but has not been checked
        return false;
    }

    /**
     * Map the fields ready for payload sent to MailerLite
     *
     * @param $field_name string
     * @param $field_mapped_name string
     * @param $submission_data array
     * @param $separator string
     *
     */
    private function doMapFields(string $field_name, string $field_mapped_name, array $submission_data, string $separator = ", ")
    {
        if (array_key_exists($field_mapped_name, $submission_data)) { // Check if the array key exists
            $field_data[] = $submission_data[$field_mapped_name];
        }

        $field_data = implode($separator, $field_data);
        $this->subscriber_data['fields'][$field_name] = $field_data;
    }
}
