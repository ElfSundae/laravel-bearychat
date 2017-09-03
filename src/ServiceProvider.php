<?php

namespace ElfSundae\BearyChat\Laravel;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($from = __DIR__.'/../config/bearychat.php', 'bearychat');

        if ($this->app->runningInConsole()) {
            $to = (function_exists('config_path')) ? config_path('bearychat.php') : base_path('config/bearychat.php');
            $this->publishes([$from => $to], 'bearychat');
        }

        $this->app->singleton('bearychat', function ($app) {
            return (new ClientManager)
                ->setDefaultName($app['config']->get('bearychat.default'))
                ->setClientsDefaults($app['config']->get('bearychat.clients_defaults'))
                ->setClientsConfig($app['config']->get('bearychat.clients'));
        });

        $this->app->alias('bearychat', ClientManager::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return ['bearychat', ClientManager::class];
    }
}
