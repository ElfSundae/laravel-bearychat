<?php

namespace ElfSundae\BearyChat\Laravel;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if (is_a($this->app, 'Laravel\Lumen\Application')) {
            $this->app->configure('bearychat');
        }

        $this->mergeConfigFrom($from = __DIR__.'/../config/bearychat.php', 'bearychat');

        if ($this->app->runningInConsole()) {
            $this->publishes([$from => base_path('config/bearychat.php')], 'bearychat');
        }

        $this->app->singleton('bearychat', function ($app) {
            return (new ClientManager)
                ->setDefaultName($app['config']->get('bearychat.default'))
                ->setClientsDefaults($app['config']->get('bearychat.clients_defaults'))
                ->setClientsConfig($app['config']->get('bearychat.clients'));
        });

        $this->app->alias('bearychat', ClientManager::class);
    }
}
