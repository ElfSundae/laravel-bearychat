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
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $appVersion = method_exists($app, 'version') ? $app->version() : $app::VERSION;

        $this->isLumen = str_contains($appVersion, 'Lumen');
        $this->isLaravel4 = (int) $appVersion == 4;
        $this->isLaravel5 = (int) $appVersion == 5;
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->isLaravel4) {
            $this->package('elfsundae/laravel-bearychat', 'bearychat', __DIR__);
        } else {
            $this->publishes([
                $this->getConfigFromPath() => $this->getConfigToPath(),
            ], 'config');
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if (! $this->isLaravel4) {
            $this->mergeConfigFrom($this->getConfigFromPath(), 'bearychat');
        }

        $this->app->singleton('bearychat', function ($app) {
            return new ClientManager($app);
        });

        $this->app->alias('bearychat', 'ElfSundae\BearyChat\Laravel\ClientManager');

        $this->aliasFacades();
    }

    /**
     * Get the source config path.
     *
     * @return string
     */
    protected function getConfigFromPath()
    {
        return __DIR__.'/config/config.php';
    }

    /**
     * Get the config destination path.
     *
     * @return string
     */
    protected function getConfigToPath()
    {
        return $this->isLumen ?
                base_path('config/bearychat.php') :
                config_path('bearychat.php');
    }

    protected function aliasFacades()
    {
        if (class_exists('Illuminate\Foundation\AliasLoader')) {
            // For Laravel
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('BearyChat', \ElfSundae\BearyChat\Laravel\Facade::class);
        } else {
            // For Lumen
            class_alias('ElfSundae\BearyChat\Laravel\Facade', 'BearyChat');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return ['bearychat'];
    }
}
