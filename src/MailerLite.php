<?php

namespace SiteRig\MailerLite;

use Illuminate\Support\Facades\Log;
use MailerLiteApi\MailerLite as MailerLiteAPI;
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
            $this->mailerlite = new MailerLiteAPI($api_key);
        }
    }

    /**
     * Get Subscriber Groups from MailerLite
     *
     * @param int $group_id The ID of the subscriber group if you want to return a single group
     *
     * @return array
     */
    public function getSubscriberGroups(int $group_id = null): array
    {
        // Connect to Groups API and get all groups
        $groups_api = $this->mailerlite->groups()->get();

        // Set empty subscriber groups array
        $subscriber_groups = [];

        // Check if there was an error getting groups
        if (property_exists($groups_api, 'error')) {

            // Add error message
            return [
                'id' => $group_id ? $group_id : 0,
                'title' => 'Error: Group(s) not found MailerLite',
            ];

        }

        // Check if there were any returned results
        if (count($groups_api) > 0) {

            // Loop through groups and put into new array
            foreach ($groups_api as &$group) {

                // Add group to array
                $subscriber_groups[] = [
                    'id' => $group->id,
                    'title' => $group->name,
                ];

            }

            // Check if this is a request for a single group
            if ($group_id) {

                // Search the returned list of groups
                if ($key = array_search($group_id, array_column($subscriber_groups, 'id'))) {

                    return [
                        'id' => $subscriber_groups[$key]['id'],
                        'title' => $subscriber_groups[$key]['title'],
                    ];

                }

            }

        }

        // Check if this is a request for a single group (when it doesn't exist)
        if ($group_id) {

            return [
                'id' => $group_id,
                'title' => 'Error: Saved group not found on MailerLite',
            ];

        }

        // Return the group(s)
        return $subscriber_groups;
    }

    /**
     * Get Subscriber Fields from MailerLite
     *
     * @param int $field_key The field ID
     *
     * @return array
     */
    public function getSubscriberFields(string $field_key = null): array
    {
        // Get the all subscriber fields
        $fields_api = $this->mailerlite->fields()->get();

        // Create new array for fields
        $subscriber_fields = [];

        // Check if there was an error getting fields
        if (property_exists($fields_api, 'error')) {

            // Add error message
            return [
                'id' => $field_key ? $field_key : 0,
                'title' => 'Error: Field(s) not found MailerLite',
            ];

        }

        // Check if there were any returned results
        if (count($fields_api) > 0) {

            // Loop through fields and put into new array
            foreach ($fields_api as &$field) {

                // Check this isn't the name, email or marketing_permissions field
                if (!($field->key == 'name' || $field->key == 'email' || $field->key == 'marketing_permissions')) {

                    // Add field to array
                    $subscriber_fields[] = [
                        'id' => $field->key,
                        'title' => $field->title
                    ];

                }

            }

            // Check if this is a request for a single field
            if ($field_key) {

                // Search the returned list of fields
                if ($key = array_search($field_key, array_column($subscriber_fields, 'key'))) {

                    return [
                        'id' => $subscriber_fields[$key]['key'],
                        'title' => $subscriber_fields[$key]['title'],
                    ];

                }

            }

        }

        // Check if this is a request for a single group (when it doesn't exist)
        if ($field_key) {

            return [
                'id' => $field_key,
                'title' => 'Error: Saved field not found on MailerLite',
            ];

        }

        // Return the array
        return $subscriber_fields;
    }

    /**
     * Add Subscriber to MailerLite
     *
     * @param array $config         The form configuration data
     * @param object $submission    The form submission object
     *
     * @return array
     */
    public function addSubscriber(array $config, object $submission_data): array
    {
        // Check if marketing permissions were accepted (returns true if not in use)
        if ($this->checkMarketingOptin($config, $submission_data)) {

            // Set data email field
            $this->subscriber_data['email'] = $submission_data->get($config['email_field']);

            if (!empty($config['name_field'])) { // Check if name_field is set
                $this->doMapFields('name', $config['name_field'], $submission_data->toArray(), ' ');
            }

            // Check for mapped fields
            if ($mapped_fields = Arr::get($config, 'mapped_fields')) {

                // Loop through mapped fields
                collect($mapped_fields)->map(function ($item, $key) use ($submission_data) {
                    if (!empty($item["mapped_form_fields"])) { // In case there is no mapped form field
                        // Check if mapped fields contain last_name
                        if ($item['subscriber_field'] == 'last_name') {
                            $this->last_name_field_exists = true;
                        }
                        $this->doMapFields($item['subscriber_field'], $item["mapped_form_fields"], $submission_data->toArray());
                    }
                });

            }

            // Check if Automatic Name Split is configured
            if (Arr::get($config, 'auto_split_name', true)) {

                // If there is no last_name field mapped
                if ($this->last_name_field_exists === false) {
                    // Split name by first space character
                    $name_array = explode(' ', $this->subscriber_data['fields']['name'], 2);

                    // Set data
                    $this->subscriber_data['fields']['name'] = $name_array[0];
                    $this->subscriber_data['fields']['last_name'] = $name_array[1] ?? '';
                }

            }

            // Set options for api parameters
            $subscriber_options = [
                'resubscribe' => true
            ];

            // Check if subscriber group was setup
            if (isset($config['subscriber_group'])) {

                // Use the MailerLite Groups API to add the subscriber to a group
                $response = $this->mailerlite->groups()->addSubscriber($config['subscriber_group'], $this->subscriber_data, $subscriber_options);

            } else {

                // Use the MailerLite Subscriber API to add the subscriber
                $response = $this->mailerlite->subscribers()->create($this->subscriber_data, $subscriber_options);

            }

            // Check response for errors
            if (property_exists($response, 'error')) {

                // Generate error to the log
                \Log::error("MailerLite - " . $response->error->message);

            } elseif (empty($response)) {

                // Generate error to the log
                \Log::error("MailerLite - Bad Request");

            }

        }

        // Return the submission
        return [
            'submission' => $submission_data
        ];
    }

    /**
     * Are there any Marketing Opt-in fields setup and have they been accepted?
     *
     * @param $config array
     * @param $submission array
     *
     * @return bool
     */
    private function checkMarketingOptin(array $config, object $submission_data): bool
    {
        // Get marketing opt-in field
        $marketing_optin = Arr::get($config, 'marketing_optin_field', false);

        // Check if marketing permission field is in submission (which indicates it's checked) or if it's not in use
        if (request()->has($marketing_optin) || !($marketing_optin)) {
            return true;
        }

        // Return false as field is setup but has not been checked
        return false;
    }

    /**
     * Combine multiple mapped fields
     *
     * @param $formset_name string
     *
     * @return mixed
     */
    private function getFormConfiguration(string $formset_name)
    {
        return collect($this->getConfig('forms'))->first(function ($ignored, $data) use ($formset_name) {
            return $formset_name == Arr::get($data, 'form');
        });
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
