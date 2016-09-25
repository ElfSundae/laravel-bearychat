<?php

namespace ElfSundae\BearyChat\Laravel\Test;

use ElfSundae\BearyChat\Client;
use ElfSundae\BearyChat\Laravel\ClientManager;
use ElfSundae\BearyChat\Laravel\ServiceProvider;
use Orchestra\Testbench\TestCase;

class ClientManagerTest extends TestCase
{
    public function testInstantiation()
    {
        $this->assertInstanceOf(ClientManager::class, new ClientManager($this->app));
    }

    public function testInstantiationOfClient()
    {
        $this->assertInstanceOf(Client::class, $this->getManager()->client());
    }

    protected function getManager()
    {
        return $this->app->make('bearychat');
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }
}
