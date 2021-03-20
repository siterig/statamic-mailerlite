<?php

namespace SiteRig\MailerLite;

use Illuminate\Support\Facades\Log;
use MailerLiteApi\MailerLite as MailerLiteAPI;
use Statamic\Events\SubmissionCreated;
use Statamic\Support\Arr;

class MailerLite
{
    public $mailerlite;

    public function __construct()
    {
        if ($api_key = config('mailerlite.api_key')) {
            $this->mailerlite = new MailerLiteAPI($api_key);
        }
    }

    public function connectMailerLite(string $endpoint, array $data = [])
    {
        return optional($this->mailerlite)->get();
    }
}
