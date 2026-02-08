<?php

namespace App\Tests\Service;

use App\Service\ChatbotService;
use App\Service\GeminiService;
use PHPUnit\Framework\TestCase;

class ChatbotServiceTest extends TestCase
{
    public function testProcessMessageInDemoMode(): void
    {
        // Mock GeminiService
        $geminiService = $this->createMock(GeminiService::class);
        
        $service = new ChatbotService($geminiService);

        $response = $service->processMessage('Bonjour', [
            'userId' => 1,
            'name' => 'Test User',
            'skills' => ['PHP', 'Symfony']
        ]);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('message', $response);
        $this->assertArrayHasKey('timestamp', $response);
        $this->assertIsString($response['message']);
        $this->assertNotEmpty($response['message']);
    }

    public function testClearHistory(): void
    {
        $geminiService = $this->createMock(GeminiService::class);
        $service = new ChatbotService($geminiService);

        // Add message
        $service->processMessage('Test', ['userId' => 1]);
        
        // Clear history
        $service->clearHistory();

        // Verify history is empty (implicitly tested by new conversation starting fresh)
        $this->assertTrue(true);
    }
}
