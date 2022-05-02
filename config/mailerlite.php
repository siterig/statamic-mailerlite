<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MailerLite API Key
    |--------------------------------------------------------------------------
    |
    | The API key for connecting to the MailerLite API.
    |
    */

    'api_key' => env('MAILERLITE_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Forms
    |--------------------------------------------------------------------------
    |
    | The form settings for submissions to add to your MailerLite list.
    |
    */

    'forms' => [
        [
            /*
            |--------------------------------------------------------------------------
            | Form
            |--------------------------------------------------------------------------
            |
            | The handle of the Statamic form to listen for submissions from.
            |
            */

            'form' => null,

            /*
            |--------------------------------------------------------------------------
            | Subscriber Group
            |--------------------------------------------------------------------------
            |
            | Optional: The subscriber group that the submission should be added to.
            |
            */

            'subscriber_group' => null,

            /*
            |--------------------------------------------------------------------------
            | Name Field
            |--------------------------------------------------------------------------
            |
            | Optional: Select the form field to use for `name`.
            |
            */

            'name_field' => null,

            /*
            |--------------------------------------------------------------------------
            | Email Field
            |--------------------------------------------------------------------------
            |
            | Select the form field to use for `name`.
            |
            */

            'email_field' => null,

            /*
            |--------------------------------------------------------------------------
            | Automatically Split Name
            |--------------------------------------------------------------------------
            |
            | Split into `name` and `last_name` on MailerLite. This setting is ignored
            | if you map `last_name` separately.
            |
            */

            'auto_split_name' => true,

            /*
            |--------------------------------------------------------------------------
            | Opt-in Field
            |--------------------------------------------------------------------------
            |
            | Optional: This field should be an un-ticked checkbox that conforms to
            | regulations in your market (e.g. GDPR/ePrivacy)
            |
            */

            'marketing_optin_field' => null,

            /*
            |--------------------------------------------------------------------------
            | Marketing Permissions Field
            |--------------------------------------------------------------------------
            |
            | Optional: To use this option you will need to setup a
            | `marketing_permissions` field on MailerLite. For more information see
            | https://help.mailerlite.com/article/show/88106-checkboxes-and-marketing-permissions
            |
            */

            'marketing_permissions_fields' => null,

            /*
            |--------------------------------------------------------------------------
            | Mapped Fields
            |--------------------------------------------------------------------------
            |
            | Optional: Add additional fields that you would like to map here
            |
            */

            'mapped_fields' => [
                [
                    /*
                    |--------------------------------------------------------------------------
                    | Subscriber Field
                    |--------------------------------------------------------------------------
                    |
                    | The field on your MailerLite audience list
                    |
                    */

                    'subscriber_field' => null,

                    /*
                    |--------------------------------------------------------------------------
                    | Form Field(s)
                    |--------------------------------------------------------------------------
                    |
                    | the form field(s) handle(s) to map to
                    |
                    */

                    'mapped_form_fields' => null,
                ],
            ],
        ],
    ],

];
