<?php

namespace ElfSundae\BearyChat\Laravel\Test;

use Orchestra\Testbench\TestCase;
use ElfSundae\BearyChat\Laravel\ClientManager;
use ElfSundae\BearyChat\Laravel\ServiceProvider;

class ServiceProviderTest extends TestCase
{
    public function testInstantiation()
    {
        $this->assertInstanceOf(
            ServiceProvider::class,
            $this->app->getProvider(ServiceProvider::class)
        );
    }

    public function testInstantiationOfClientManager()
    {
        $this->assertInstanceOf(
            ClientManager::class,
            $this->app->make('bearychat')
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }
}
