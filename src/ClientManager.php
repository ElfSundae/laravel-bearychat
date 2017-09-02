<?php

namespace ElfSundae\BearyChat\Laravel;

use Closure;
use Illuminate\Support\Arr;
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
     * The default client name.
     *
     * @var string
     */
    protected $defaultName;

    /**
     * The defaults for all clients.
     *
     * @var array
     */
    protected $clientsDefaults = [];

    /**
     * The clients config.
     *
     * @var array
     */
    protected $clientsConfig = [];

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
     * Create a new client manager instance.
     *
     * @param  mixed  $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Get the default client name.
     *
     * @return string
     */
    public function getDefaultName()
    {
        return $this->defaultName ?: Arr::first(array_keys($this->clientsConfig));
    }

    /**
     * Set the default client name.
     *
     * @param  string  $name
     * @return $this
     */
    public function setDefaultName($name)
    {
        $this->defaultName = $name;

        return $this;
    }

    /**
     * Get the clients defaults.
     *
     * @return array
     */
    public function getClientsDefaults()
    {
        return $this->clientsDefaults;
    }

    /**
     * Set the clients defaults.
     *
     * @param  array  $defaults
     * @return $this
     */
    public function setClientsDefaults($defaults)
    {
        if (is_array($defaults)) {
            $this->clientsDefaults = $defaults;
        }

        return $this;
    }

    /**
     * Get the clients config.
     *
     * @return array
     */
    public function getClientsConfig()
    {
        return $this->clientsConfig;
    }

    /**
     * Set the clients config.
     *
     * @param  array  $config
     * @return $this
     */
    public function setClientsConfig($config)
    {
        if (is_array($config)) {
            $this->clientsConfig = $config;
        }

        return $this;
    }

    /**
     * Get a client instance.
     *
     * @param  string|null  $name
     * @return \ElfSundae\BearyChat\Client
     */
    public function client($name = null)
    {
        if (is_null($name)) {
            $name = $this->getDefaultName();
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
        $config = $this->getConfigForClient($name);

        return new Client(
            $config['webhook'],
            $config['message_defaults'],
            $this->getHttpClient($name)
        );
    }

    /**
     * Get client config for the given client name.
     *
     * @param  string  $name
     * @return array
     */
    protected function getConfigForClient($name)
    {
        $config = $this->clientsConfig[$name];

        if (empty($config['webhook'])) {
            $config['webhook'] = Arr::get($this->clientsDefaults, 'webhook');
        }

        $config['message_defaults'] = array_merge(
            Arr::get($this->clientsDefaults, 'message_defaults', []),
            Arr::get($config, 'message_defaults', [])
        );

        return $config;
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
}
