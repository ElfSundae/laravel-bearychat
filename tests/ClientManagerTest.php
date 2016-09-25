<?php

namespace ElfSundae\BearyChat\Laravel\Test;

use ElfSundae\BearyChat\Client;
use ElfSundae\BearyChat\Message;
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

    public function testDynamicallyCall()
    {
        $this->assertInstanceOf(Message::class, $this->getManager()->text('foo'));
    }

    public function testResolvedDefaultClient()
    {
        $this->app['config']->set('bearychat.default', 'foo');

        $this->assertSame($this->getClient(), $this->getClient('foo'));
    }

    public function testClientConfig()
    {
        $this->app['config']->set('bearychat', [
            'default' => 'foo',
            'clients' => [
                'foo' => [
                    'webhook' => 'fake.endpoint',
                    'message_defaults' => [
                        'user' => 'elf',
                    ],
                ],
            ],
        ]);

        $client = $this->getClient();

        $this->assertSame('fake.endpoint', $client->getWebhook());

        $this->assertSame('elf', $client->getMessageDefaults('user'));
    }

    protected function getManager()
    {
        return $this->app->make('bearychat');
    }

    protected function getClient($name = null)
    {
        return $this->getManager()->client($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }
}
