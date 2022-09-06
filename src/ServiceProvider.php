<?php

namespace SiteRig\MailerLite;

use Edalzell\Forma\Forma;
use SiteRig\MailerLite\Fieldtypes\FormFields;
use SiteRig\MailerLite\Fieldtypes\SubscriberField;
use SiteRig\MailerLite\Fieldtypes\SubscriberGroup;
use SiteRig\MailerLite\Http\Controllers\ConfigController;
use SiteRig\MailerLite\Listeners\FormSubmission;
use Statamic\Events\SubmissionCreated;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $fieldtypes = [
        FormFields::class,
        SubscriberField::class,
        SubscriberGroup::class,
    ];

    protected $listen = [
        SubmissionCreated::class => [FormSubmission::class],
    ];

    protected $routes = [
        'cp' => __DIR__ . '/../routes/cp.php',
    ];

    protected $scripts = [
        __DIR__ . '/../resources/dist/js/cp.js',
    ];

    public function boot()
    {
        parent::boot();

        Forma::add('siterig/mailerlite', ConfigController::class);

        $this->app->booted(function () {

            $this->publishes([
                __DIR__ . '/../config/mailerlite.php' => config_path('mailerlite.php'),
            ], 'config');

        });
    }
}
