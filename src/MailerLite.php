<?php

namespace SiteRig\MailerLite;

use Illuminate\Support\Facades\Log;
use MailerLiteApi\MailerLite as MailerLiteAPI;
use Statamic\Support\Arr;

class MailerLite
{
    private $mailerlite = null;

    public function __construct()
    {
        if ($api_key = config('mailerlite.api_key')) {
            $this->mailerlite = new MailerLiteAPI($api_key);
        }
    }

    public function getSubscriberGroups(int $group_id = null)
    {
        // Connect to Groups API
        $groups_api = $this->mailerlite->groups();

        // Check if this is a request for a single group
        if ($group_id) {

            // Get single group
            return $groups_api->find($group_id);

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

            // Return the array
            return $subscriber_groups;

        }
    }

    public function getSubscriberFields()
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
}
