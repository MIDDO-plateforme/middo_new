<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FileUploadControllerTest extends WebTestCase
{
    public function testStorageStatsEndpoint(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/storage/stats');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('total_size', $data);
        $this->assertArrayHasKey('documents', $data);
        $this->assertArrayHasKey('images', $data);
        $this->assertArrayHasKey('avatars', $data);
    }
}
