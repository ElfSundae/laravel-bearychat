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
    protected $defer = false;

    protected $isLaravel4 = false;

    protected $isLumen = false;

    protected $isLaravel5 = false;

    /**
     * Create a new service provider instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $appVersion = $app::VERSION;

        $this->isLumen = str_contains($appVersion, 'Lumen');
        $this->isLaravel4 = (int)$appVersion == 4;
        $this->isLaravel5 = (int)$appVersion == 5;
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->isLaravel4) {
            $this->package('elfsundae/bearychat-laravel', 'bearychat', __DIR__);
        } else {
            $this->publishes([
                __DIR__.'/config/config.php' => config_path('bearychat.php')
            ]);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if ($this->isLaravel5) {
            $this->mergeConfigFrom(__DIR__.'/config/config.php', 'bearychat');
        }

        $this->app->singleton('bearychat', function () {
            return new ClientManager($this->app);
        });

        $this->app->alias('bearychat', 'ElfSundae\BearyChat\Laravel\ClientManager');

        if (class_exists('Illuminate\Foundation\AliasLoader')) {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('BearyChat', \ElfSundae\BearyChat\Laravel\Facade::class);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['bearychat'];
    }
}
