<?php

namespace SiteRig\MailerLite;

use MailerLiteApi\MailerLite as MailerLiteAPI;
use Statamic\Events\SubmissionCreated;
use Statamic\Facades\Permission;
use Statamic\Facades\User;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Support\Arr;

class ServiceProvider extends AddonServiceProvider
{
    protected $commands = [
        GetGroups::class,
        GetMergeFields::class,
    ];

    protected $fieldtypes = [

    ];

    protected $routes = [
        'cp' => __DIR__ . '/../routes/cp.php',
    ];

    public function boot()
    {
        parent::boot();

        $this
            ->bootAddonConfig()
            ->bootAddonViews()
            ->bootAddonTranslations()
            ->bootAddonPermissions();
    }

    protected function bootAddonConfig()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/mailerlite.php', 'statamic.mailerlite');

        $this->publishes([
            __DIR__ . '/../config/mailerlite.php' => config_path('statamic/mailerlite.php'),
        ], 'mailerlite-config');

        return $this;
    }

    protected function bootAddonViews()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'mailerlite');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/mailerlite'),
        ], 'mailerlite-views');

        return $this;
    }

    protected function bootAddonTranslations()
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'mailerlite');

        return $this;
    }

    protected function bootAddonPermissions()
    {
        $this->app->booted(function () {
            Permission::group('mailerlite', 'MailerLite', function () {
                Permission::register('edit mailerlite settings')->label(__('mailerlite::messages.edit_mailerlite_settings'));
            });
        });

        return $this;
    }

    private function mailerlitePermissions()
    {
        $user = User::current();

        return $user->can('edit mailerlite settings');
    }
}
