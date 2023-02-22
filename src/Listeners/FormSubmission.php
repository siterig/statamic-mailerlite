<?php

namespace SiteRig\MailerLite\Listeners;

use SiteRig\MailerLite\MailerLite;
use Statamic\Events\SubmissionCreated;

class FormSubmission
{
    private $mailerlite = null;

    public function handle(SubmissionCreated $event)
    {
        $this->mailerlite = new MailerLite;
        $this->mailerlite->addSubscriber($this->getFormConfig($event->submission->form()->handle()), $event->submission->data());
    }

    private function getFormConfig(string $handle)
    {
        return collect(config('mailerlite.forms', []))->firstWhere('form', $handle) ?? [];
    }
}
