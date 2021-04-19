<?php

namespace SiteRig\MailerLite;

use Edalzell\Forma\Forma;
use SiteRig\MailerLite\Fieldtypes\SubscriberField;
use SiteRig\MailerLite\Fieldtypes\SubscriberGroup;
use SiteRig\MailerLite\Http\Controllers\ConfigController;
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

        $this->mergeConfigFrom(__DIR__ . '/../config/mailerlite.php', 'mailerlite');
        $this->publishes([
            __DIR__ . '/../config/mailerlite.php' => config_path('mailerlite.php'),
        ], 'config');

        $this->app->booted(function () {
            Forma::add('siterig/mailerlite', ConfigController::class);
        });
    }
}
