<?php

namespace ElfSundae\BearyChat\Laravel\Test;

use Exception;
use Mockery as m;
use ElfSundae\BearyChat\Client;
use ElfSundae\BearyChat\Message;
use Orchestra\Testbench\TestCase;
use GuzzleHttp\Client as HttpClient;
use ElfSundae\BearyChat\Laravel\ServiceProvider;

class ClientManagerTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    /**
     * {@inheritdoc}
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']['bearychat'] = [
            'default' => 'default-client',
            'clients_defaults' => [
                'webhook' => 'http://default/webhook',
                'message_defaults' => [
                    'user' => 'default-user',
                ],
            ],
            'clients' => [
                'default-client' => [],
                'foo-client' => [
                    'webhook' => 'http://foo/webhook',
                    'message_defaults' => [
                        'channel' => 'foo-channel',
                    ],
                ],
            ],
        ];
    }

    public function testConfig()
    {
        $this->assertEquals('default-client', $this->getManager()->getDefaultName());
        $this->assertEquals([
            'webhook' => 'http://default/webhook',
            'message_defaults' => [
                'user' => 'default-user',
            ],
        ], $this->getManager()->getClientsDefaults());
    }

    public function testGetClient()
    {
        $this->assertInstanceOf(Client::class, $this->getClient());
        $this->assertEquals('http://default/webhook', $this->getClient()->getWebhook());
        $this->assertEquals([
            'user' => 'default-user',
        ], $this->getClient()->getMessageDefaults());
        $this->assertSame($this->getClient(), $this->getClient());

        $defaultClient = $this->getManager()->client($this->getManager()->getDefaultName());
        $this->assertSame($defaultClient, $this->getClient());

        $this->assertInstanceOf(Client::class, $this->getClient('foo-client'));
        $this->assertEquals('http://foo/webhook', $this->getClient('foo-client')->getWebhook());
        $this->assertEquals([
            'user' => 'default-user',
            'channel' => 'foo-channel',
        ], $this->getClient('foo-client')->getMessageDefaults());
        $this->assertSame($this->getClient('foo-client'), $this->getClient('foo-client'));
    }

    public function testCustomHttpClient()
    {
        $httpClient = m::mock(HttpClient::class)
            ->shouldReceive('post')
            ->andThrow(MyException::class)
            ->mock();

        $this->getManager()->customHttpClient(function ($name) use ($httpClient) {
            $this->assertEquals('foo-client', $name);

            return $httpClient;
        });

        $this->expectException(MyException::class);
        $this->getClient('foo-client')->send();

        $this->getManager()->customHttpClient(null);
    }

    public function testDynamicallyCall()
    {
        $message = $this->getManager()->text('foo');
        $this->assertInstanceOf(Message::class, $message);
    }

    protected function getManager()
    {
        return $this->app->make('bearychat');
    }

    protected function getClient($name = null)
    {
        return $this->getManager()->client($name);
    }
}

class MyException extends Exception
{
}
