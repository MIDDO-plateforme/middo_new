<?php

namespace App\Bundle\MIDDOGeniusProBundle\Service;

use Psr\Log\LoggerInterface;

class GeniusProApiService
{
    private LoggerInterface $logger;
    private string $apiBaseUrl;

    public function __construct(
        LoggerInterface $logger,
        string $geniusProApiUrl = 'http://localhost:8000'
    ) {
        $this->logger = $logger;
        $this->apiBaseUrl = $geniusProApiUrl;
    }

    /**
     * Vérifie la santé du serveur Genius Pro
     */
    public function checkHealth(): bool
    {
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'ignore_errors' => true
                ]
            ]);

            $response = @file_get_contents($this->apiBaseUrl, false, $context);
            
            if ($response === false) {
                return false;
            }

            $data = json_decode($response, true);
            return isset($data['status']) && $data['status'] === 'operational';

        } catch (\Exception $e) {
            $this->logger->error('Genius Pro health check failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoie un message à l'agent IA
     */
    public function sendMessage(string $message, string $expertise = 'fullstack', ?string $context = null): ?array
    {
        try {
            $payload = json_encode([
                'message' => $message,
                'expertise' => $expertise,
                'context' => $context ?? 'MIDDO Platform'
            ]);

            $streamContext = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-Type: application/json',
                    'content' => $payload,
                    'timeout' => 30,
                    'ignore_errors' => true
                ]
            ]);

            $response = @file_get_contents($this->apiBaseUrl . '/chat', false, $streamContext);

            if ($response === false) {
                $this->logger->error('Genius Pro API: Failed to get response from /chat');
                return null;
            }

            $data = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->logger->error('Genius Pro API: Invalid JSON response: ' . json_last_error_msg());
                return null;
            }

            return $data;

        } catch (\Exception $e) {
            $this->logger->error('Genius Pro API error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Analyse du code
     */
    public function analyzeCode(string $code, string $expertise = 'fullstack'): ?array
    {
        try {
            $payload = json_encode([
                'code' => $code,
                'expertise' => $expertise
            ]);

            $streamContext = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-Type: application/json',
                    'content' => $payload,
                    'timeout' => 30,
                    'ignore_errors' => true
                ]
            ]);

            $response = @file_get_contents($this->apiBaseUrl . '/analyze', false, $streamContext);

            if ($response === false) {
                return null;
            }

            return json_decode($response, true);

        } catch (\Exception $e) {
            $this->logger->error('Genius Pro analyze error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupère les expertises disponibles
     */
    public function getExpertises(): array
    {
        return [
            'ai_ml' => [
                'name' => 'AI/ML Expert',
                'icon' => '🤖',
                'description' => 'Expert en Intelligence Artificielle et Machine Learning'
            ],
            'fullstack' => [
                'name' => 'Full-Stack Developer',
                'icon' => '💻',
                'description' => 'Expert Symfony 6.3, PHP 8.3, Doctrine ORM'
            ],
            'security' => [
                'name' => 'Security Architect',
                'icon' => '🔐',
                'description' => 'Expert en sécurité applicative et audits'
            ],
            'business' => [
                'name' => 'Business Strategist',
                'icon' => '📈',
                'description' => 'Expert en stratégie business et growth'
            ],
            'finance' => [
                'name' => 'Financial Advisor',
                'icon' => '💰',
                'description' => 'Expert en gestion financière startup'
            ],
            'training' => [
                'name' => 'Training Coach',
                'icon' => '🎓',
                'description' => 'Expert en formation et documentation'
            ]
        ];
    }
}
