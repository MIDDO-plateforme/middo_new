<?php

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SuggestionsControllerTest extends WebTestCase
{
    /**
     * Test 1 : Vérifier que l'endpoint /api/suggestions retourne du JSON
     */
    public function testSuggestionsEndpointReturnsJson(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/suggestions', [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['context' => 'startup fintech blockchain'])
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    /**
     * Test 2 : Vérifier la structure de la réponse
     */
    public function testSuggestionsResponseStructure(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/suggestions', [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['context' => 'tech startup'])
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('success', $data);
        $this->assertArrayHasKey('suggestions', $data);
    }

    /**
     * Test 3 : Vérifier l'endpoint /api/suggestions/analyze
     */
    public function testAnalyzeEndpointWorks(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/suggestions/analyze', [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['context' => 'ai development'])
        );

        $this->assertResponseIsSuccessful();
        
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('success', $data);
    }
}