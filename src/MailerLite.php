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
        $this->mailerlite = new MailerLiteAPI(config('mailerlite.api_key'));
    }
}
