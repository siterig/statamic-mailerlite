<?php

namespace SiteRig\MailerLite;

use Illuminate\Support\Facades\Log;
use MailerLiteApi\MailerLite as MailerLiteAPI;
use Statamic\Facades\Blueprint;
use Statamic\Support\Arr;

class MailerLite
{
    private $mailerlite = null;

    private $form = null;

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
     * @param $group_id int
     *
     * @return array
     */
    public function getSubscriberGroups(int $group_id = null)
    {
        // Connect to Groups API
        $groups_api = $this->mailerlite->groups();

        // Check if this is a request for a single group
        if ($group_id) {

            // Get single group
            $group = $groups_api->find($group_id);

            // Check if there was an error getting this group by id
            if (property_exists($group, 'error')) {

                // Add error message
                $subscriber_groups = [
                    'id' => $group_id,
                    'title' => 'Error: group no longer exists',
                ];

            } else {

                // Add group to array
                $subscriber_groups = [
                    'id' => $group->id,
                    'title' => $group->name,
                ];

            }

        } else {

            // Get all groups
            $all_groups = $groups_api->get();

            // Create new array for groups
            $subscriber_groups = [];

            // Loop through groups and put into new array
            foreach ($all_groups as &$group) {

                // Add group to array
                $subscriber_groups[] = [
                    'id' => $group->id,
                    'title' => $group->name,
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
        $fields_api = $this->mailerlite->fields();
        $all_fields = $fields_api->get();

        // Create new array for fields
        $subscriber_fields = [];

        // Loop through fields and put into new array
        foreach ($all_fields as &$field) {

            // Check this isn't the name, email or marketing_permissions field
            if (!($field->key == 'name' || $field->key == 'email' || $field->key == 'marketing_permissions')) {

                // Add field to array
                $subscriber_fields[] = [
                    'id' => $field->key,
                    'title' => $field->title
                ];

            }

        }

        // Return the array
        return $subscriber_fields;
    }

    /**
     * Add Subscriber to MailerLite
     *
     * @param $config array
     * @param $submission array
     *
     * @return array
     */
    public function addSubscriber(array $config, object $submission_data)
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
    private function checkMarketingOptin(array $config, object $submission_data)
    {
        // Get marketing opt-in field
        $marketing_optin = Arr::get($config, 'marketing_optin_field', false);

        // Check if marketing permission field is in submission (which indicates it's checked) or if it's not in use
        //if (request()->has($marketing_optin) || !($marketing_optin)) {
        if (request()->has($marketing_optin)) {
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
