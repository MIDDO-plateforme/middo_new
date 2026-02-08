<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NotificationControllerTest extends WebTestCase
{
    public function testGetNotificationsEndpoint(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/notifications');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
    }

    public function testUnreadCountEndpoint(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/notifications/unread/count');

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('count', $data);
        $this->assertIsInt($data['count']);
    }
}
