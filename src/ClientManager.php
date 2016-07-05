<?php

namespace ElfSundae\BearyChat\Laravel;

use ElfSundae\BearyChat\Client;

class ClientManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The array of resolved BearyChat clients.
     *
     * @var array
     */
    protected $clients = [];

    protected $isLaravel4 = false;

    /**
     * Create a new client manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;

        $appVersion = method_exists($app, 'version') ? $app->version() : $app::VERSION;

        $this->isLaravel4 = (int)$appVersion == 4;
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
    public function client($name = 'default')
    {
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
        $config = $this->getConfig($name);

        return new Client(
            $config['webhook'],
            isset($config['message_defaults']) ? $config['message_defaults'] : []
        );
    }

    /**
     * Get the BearyChat client configuration.
     *
     * @param  string  $name
     * @return array
     */
    protected function getConfig($name)
    {
        if ($this->isLaravel4) {
            return $this->app['config']->get("bearychat::{$name}");
        }

        return $this->app['config']["bearychat.{$name}"];
    }
}
