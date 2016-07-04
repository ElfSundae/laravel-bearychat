<?php

namespace ElfSundae\BearyChat\Laravel;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProviderLaravel5 extends LaravelServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('bearychat.php')
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'bearychat');

        $this->app->singleton('bearychat', function () {
            return new ClientManager($this->app);
        });

        $this->app->alias('bearychat', 'ElfSundae\BearyChat\Laravel\ClientManager');
    }
}
