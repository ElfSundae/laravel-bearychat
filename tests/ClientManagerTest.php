<?php

namespace ElfSundae\BearyChat\Laravel\Test;

use ElfSundae\BearyChat\Client;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client as HttpClient;
use ElfSundae\BearyChat\Laravel\ClientManager;

class ClientManagerTest extends TestCase
{
    public function testInstantiation()
    {
        $this->assertInstanceOf(ClientManager::class, new ClientManager);
    }

    public function testConfig()
    {
        $manager = new ClientManager;
        $manager->setDefaultName('foo');
        $this->assertSame('foo', $manager->getDefaultName());

        $manager->setClientsDefaults(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $manager->getClientsDefaults());

        $manager->setClientsConfig(['client' => 'foo']);
        $this->assertEquals(['client' => 'foo'], $manager->getClientsConfig());

        $manager->setDefaultName(null);
        $this->assertSame('client', $manager->getDefaultName());
    }

    public function testClient()
    {
        $manager = (new ClientManager)
            ->setClientsDefaults([
                'webhook' => 'fake://webhook',
                'message_defaults' => [
                    'user' => 'elf',
                ],
            ])
            ->setClientsConfig([
                'foo' => [
                    'message_defaults' => [
                        'user' => 'xxc',
                        'color' => '#fff',
                    ],
                ],
                'bar' => [
                    'webhook' => 'bar://webhook',
                    'message_defaults' => [
                        'channel' => 'channel',
                    ],
                ],
            ]);

        $this->assertInstanceOf(Client::class, $manager->client());
        $this->assertSame($manager->client(), $manager->client('foo'));
        $this->assertSame('fake://webhook', $manager->client()->getWebhook());
        $this->assertSame($manager->getWebhookForClient('foo'), $manager->client()->getWebhook());
        $this->assertSame('bar://webhook', $manager->client('bar')->getWebhook());
        $this->assertEquals([
            'user' => 'xxc',
            'color' => '#fff',
        ], $manager->client()->getMessageDefaults());
        $this->assertEquals([
            'user' => 'elf',
            'channel' => 'channel',
        ], $manager->client('bar')->getMessageDefaults());
        $this->assertEquals($manager->getMessageDefaultsForClient('bar'), $manager->client('bar')->getMessageDefaults());
    }

    public function testCustomHttpClient()
    {
        $httpClient = new HttpClient;

        $manager = (new ClientManager)
            ->setClientsConfig(['foo' => []])
            ->customHttpClient(function ($name) use ($httpClient) {
                $this->assertSame('foo', $name);

                return $httpClient;
            });

        $this->assertSame($httpClient, $manager->client()->getHttpClient());
    }

    public function testDynamicallyCall()
    {
        $manager = (new ClientManager)
            ->setClientsConfig(['foo' => []]);
        $client = $manager->client();

        $this->assertSame($client, $manager->webhook('webhook'));
        $this->assertSame('webhook', $manager->getWebhook());
    }
}
