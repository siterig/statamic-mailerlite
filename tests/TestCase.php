<?php

namespace SiteRig\MailerLite\Tests;

use Statamic\Facades\Entry;
use Statamic\Facades\Collection;
use Statamic\Extend\Manifest;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Damcclean\Likes\ServiceProvider;
use Statamic\Providers\StatamicServiceProvider;
use Statamic\Statamic;
use Statamic\Facades\User;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends OrchestraTestCase
{
    use WithFaker;

    protected function getPackageProviders($app)
    {
        return [
            StatamicServiceProvider::class,
            ServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Statamic' => Statamic::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app->make(Manifest::class)->manifest = [
            'siterig/mailerlite' => [
                'id' => 'siterig/mailerlite',
                'namespace' => 'Siterig\\Mailerlite\\',
            ],
        ];

        Statamic::pushActionRoutes(function() {
            return require_once realpath(__DIR__.'/../routes/actions.php');
        });
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $configs = [
            'assets', 'cp', 'forms', 'static_caching',
            'sites', 'stache', 'system', 'users'
        ];

        foreach ($configs as $config) {
            $app['config']->set("statamic.$config", require(__DIR__."/../vendor/statamic/cms/config/{$config}.php"));
        }

        $app['config']->set('statamic.users.repository', 'file');
        $app['config']->set('statamic.stache', require(__DIR__.'/__fixtures__/config/statamic/stache.php'));
    }

    protected function makeUser()
    {
        return User::make()
            ->id((new \Statamic\Stache\Stache())->generateId())
            ->email($this->faker->email)
            ->save();
    }

    protected function makeCollection(string $handle, string $name)
    {
        Collection::make($handle)
            ->title($name)
            ->pastDateBehavior('public')
            ->futureDateBehavior('private')
            ->save();

        return Collection::findByHandle($handle);
    }

    protected function makeEntry(string $collectionHandle)
    {
        $slug = $this->faker->slug;

        Entry::make()
            ->collection($collectionHandle)
            ->blueprint('default')
            ->locale('default')
            ->published(true)
            ->slug($slug)
            ->data([
                'likes' => [],
            ])
            ->set('updated_by', User::all()->first()->id())
            ->set('updated_at', now()->timestamp)
            ->save();

        return Entry::findBySlug($slug, $collectionHandle);
    }
}
