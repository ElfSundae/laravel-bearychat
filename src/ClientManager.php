<?php

namespace ElfSundae\BearyChat\Laravel;

use Closure;
use ElfSundae\BearyChat\Client;

class ClientManager
{
    /**
     * The application instance.
     *
     * @var mixed
     */
    protected $app;

    /**
     * The array of resolved BearyChat clients.
     *
     * @var array
     */
    protected $clients = [];

    /**
     * The registered custom HTTP client creator.
     *
     * @var \Closure
     */
    protected $httpClientCreator;

    /**
     * Indicate whether the application version is Laravel 4.
     *
     * @var bool
     */
    protected $isLaravel4 = false;

    /**
     * Create a new client manager instance.
     *
     * @param  mixed  $app
     */
    public function __construct($app)
    {
        $this->app = $app;

        $appVersion = method_exists($app, 'version') ? $app->version() : $app::VERSION;

        $this->isLaravel4 = (int) $appVersion == 4;
    }

    /**
     * Dynamically call the default client instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->client(), $method], $parameters);
    }

    /**
     * Get a client instance.
     *
     * @param  string  $name
     * @return \ElfSundae\BearyChat\Client
     */
    public function client($name = null)
    {
        if (is_null($name)) {
            $name = $this->getConfig('default');
        }

        return $this->clients[$name] = $this->get($name);
    }

    /**
     * Attempt to get the client.
     *
     * @param  string  $name
     * @return \ElfSundae\BearyChat\Client
     */
    protected function get($name)
    {
        return isset($this->clients[$name]) ? $this->clients[$name] : $this->resolve($name);
    }

    /**
     * Resolve the given client.
     *
     * @param  string  $name
     * @return \ElfSundae\BearyChat\Client
     */
    protected function resolve($name)
    {
        $config = $this->getConfig('clients.'.$name);

        return new Client(
            $config['webhook'],
            isset($config['message_defaults']) ? $config['message_defaults'] : [],
            $this->getHttpClient($name)
        );
    }

    /**
     * Get the BearyChat configuration.
     *
     * @param  string  $name
     * @return mixed
     */
    protected function getConfig($name)
    {
        if ($this->isLaravel4) {
            return $this->app['config']->get("bearychat::{$name}");
        }

        return $this->app['config']["bearychat.{$name}"];
    }

    /**
     * Get the HTTP client.
     *
     * @return \GuzzleHttp\Client|null
     */
    protected function getHttpClient($name)
    {
        if ($creator = $this->httpClientCreator) {
            return $creator($name);
        }
    }

    /**
     * Register a custom HTTP client creator Closure.
     *
     * @param  \Closure  $creator
     * @return $this
     */
    public function customHttpClient(Closure $creator)
    {
        $this->httpClientCreator = $creator;

        return $this;
    }
}
