<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WorkspaceControllerTest extends WebTestCase
{
    public function testList(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/workspaces');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('code', $data[0]);
    }

    public function testHealth(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/workspaces/health');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('checked_at', $data);
    }

    public function testDetect(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/workspaces/detect');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('context', $data);
        $this->assertArrayHasKey('result', $data);
    }

    public function testAnalyzeLogs(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/workspaces/logs/analyze');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('summary', $data);
        $this->assertArrayHasKey('errors', $data);
        $this->assertArrayHasKey('warnings', $data);
    }
}
