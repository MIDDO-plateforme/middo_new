<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ChatbotControllerTest extends WebTestCase
{
    public function testChatbotMessageSuccess(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/chatbot/message', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'message' => 'Bonjour MIDDO',
            'context' => 'general'
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('response', $responseData);
        $this->assertArrayHasKey('sentiment', $responseData);
        $this->assertArrayHasKey('timestamp', $responseData);
        $this->assertNotEmpty($responseData['response']);
    }

    public function testChatbotMessageMissingParameter(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/chatbot/message', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testChatbotHistorySuccess(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/chatbot/history', [
            'userId' => 1,
            'limit' => 10
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('messages', $responseData);
        $this->assertArrayHasKey('total', $responseData);
    }

    public function testChatbotContextAnalysis(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/chatbot/analyze-context', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'messages' => [
                ['role' => 'user', 'content' => 'Je cherche une mission'],
                ['role' => 'assistant', 'content' => 'Quel type de mission ?']
            ]
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('intent', $responseData);
        $this->assertArrayHasKey('entities', $responseData);
    }
}
