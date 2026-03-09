<?php

namespace App\Tests\Service;

use App\Service\AnthropicService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AnthropicServiceTest extends TestCase
{
    /**
     * Test : Vérifier que le service peut être instancié
     */
    public function testServiceCanBeInstantiated(): void
    {
        // Mock du Logger
        $logger = $this->createMock(LoggerInterface::class);
        
        // Créer le service
        $service = new AnthropicService('fake-api-key-for-test', $logger);
        
        // Vérifications
        $this->assertInstanceOf(AnthropicService::class, $service);
        $this->assertIsObject($service);
    }
}