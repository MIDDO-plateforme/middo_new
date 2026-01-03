<?php

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MatchingControllerTest extends WebTestCase
{
    /**
     * Test 1 : Vérifier que l'endpoint /api/matching retourne du JSON
     */
    public function testMatchingEndpointReturnsJson(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/matching', [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['profile' => 'développeur fullstack Symfony React'])
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    /**
     * Test 2 : Vérifier la structure de la réponse
     */
    public function testMatchingResponseStructure(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/matching', [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['profile' => 'symfony backend developer'])
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('success', $data);
        $this->assertArrayHasKey('matches', $data);
    }

    /**
     * Test 3 : Vérifier l'endpoint /api/matching/find-profiles
     */
    public function testFindProfilesEndpointWorks(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/matching/find-profiles', [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'profile' => 'symfony expert',
                'experience_min' => 5
            ])
        );

        $this->assertResponseIsSuccessful();
        
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('success', $data);
        $this->assertArrayHasKey('profiles_found', $data);
    }
}