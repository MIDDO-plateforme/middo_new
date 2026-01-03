<?php

namespace App\Service\AI;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

class ChatbotService
{
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;
    private string $openaiApiKey;
    private string $anthropicApiKey;
    private array $conversationHistory = [];

    public function __construct(
        HttpClientInterface $httpClient,
        LoggerInterface $logger,
        EntityManagerInterface $entityManager,
        string $openaiApiKey,
        string $anthropicApiKey
    ) {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->openaiApiKey = $openaiApiKey;
        $this->anthropicApiKey = $anthropicApiKey;
    }

    public function chat(string $userMessage, ?array $userContext = null, ?string $sessionId = null): array
    {
        try {
            $systemPrompt = "You are MIDDO Assistant, a helpful AI for the MIDDO collaborative platform. Respond in French in a friendly, encouraging, and innovative Gen Z style.";
            
            if ($userContext) {
                $systemPrompt .= "\n\nUser context: " . json_encode($userContext, JSON_PRETTY_PRINT);
            }

            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->openaiApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userMessage]
                    ],
                    'temperature' => 0.8,
                    'max_tokens' => 1000
                ]
            ]);

            $data = $response->toArray();
            $content = $data['choices'][0]['message']['content'] ?? 'Désolé, je ne peux pas répondre pour le moment.';

            return [
                'success' => true,
                'response' => $content,
                'intent' => 'general',
                'suggested_actions' => [],
                'provider' => 'openai',
                'timestamp' => new \DateTime()
            ];
        } catch (\Exception $e) {
            $this->logger->error('Chatbot failed', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'response' => 'Je suis là pour t\'aider avec MIDDO ! Pose-moi une question. 😊',
                'intent' => 'unknown',
                'suggested_actions' => [
                    ['text' => '➕ Créer un workspace', 'action' => 'navigate', 'target' => '/workspace/new']
                ],
                'provider' => 'fallback',
                'timestamp' => new \DateTime()
            ];
        }
    }

    public function analyzeIntent(string $userMessage): array
    {
        return [
            'success' => true,
            'intent' => 'general',
            'confidence' => 0.5,
            'entities' => [],
            'suggested_route' => null,
            'parameters' => [],
            'provider' => 'fallback'
        ];
    }

    public function generateContextualSuggestions(?array $userContext = null): array
    {
        return [
            'success' => true,
            'suggestions' => [
                ['text' => 'Créer un workspace', 'action' => 'navigate', 'target' => '/workspace/new', 'icon' => '➕'],
                ['text' => 'Voir mes workspaces', 'action' => 'navigate', 'target' => '/workspace', 'icon' => '📁']
            ],
            'provider' => 'fallback'
        ];
    }

    public function intelligentSearch(string $query, ?array $userContext = null): array
    {
        return [
            'success' => true,
            'search_type' => 'general',
            'filters' => [],
            'suggested_results' => [],
            'alternative_searches' => [],
            'provider' => 'fallback'
        ];
    }

    public function resetConversation(?string $sessionId = null): void
    {
        $this->conversationHistory = [];
        $this->logger->info('Conversation reset', ['session_id' => $sessionId]);
    }
}
