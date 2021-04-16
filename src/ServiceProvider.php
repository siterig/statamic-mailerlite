<?php

namespace SiteRig\MailerLite;

use Edalzell\Forma\Forma;
use SiteRig\MailerLite\Http\Controllers\ConfigController;
use SiteRig\MailerLite\Fieldtypes\SubscriberField;
use SiteRig\MailerLite\Fieldtypes\SubscriberGroup;
use Statamic\Events\SubmissionCreated;
use Statamic\Facades\User;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Support\Arr;

class ServiceProvider extends AddonServiceProvider
{
    protected $fieldtypes = [
        SubscriberField::class,
        SubscriberGroup::class,
    ];

    public function boot()
    {
        parent::boot();

        $this->app->booted(function () {
            Forma::add('siterig/mailerlite', ConfigController::class);
        });
    }
}
