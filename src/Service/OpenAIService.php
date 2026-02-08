<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class OpenAIService
{
    private const API_BASE_URL = 'https://api.openai.com/v1';
    private const DEFAULT_MODEL = 'gpt-4o-mini';
    private const MAX_TOKENS = 2000;
    private const TEMPERATURE = 0.7;
    
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;
    private string $apiKey;
    
    public function __construct(
        HttpClientInterface $httpClient,
        LoggerInterface $logger,
        string $openaiApiKey
    ) {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->apiKey = $openaiApiKey;
    }
    
    public function chat(array $messages, ?string $model = null, array $options = []): array
    {
        try {
            $model = $model ?? self::DEFAULT_MODEL;
            
            $payload = [
                'model' => $model,
                'messages' => $messages,
                'temperature' => $options['temperature'] ?? self::TEMPERATURE,
                'max_tokens' => $options['max_tokens'] ?? self::MAX_TOKENS,
            ];
            
            if (isset($options['response_format'])) {
                $payload['response_format'] = $options['response_format'];
            }
            
            $this->logger->info('OpenAI API Request', [
                'model' => $model,
                'messages_count' => count($messages),
            ]);
            
            $response = $this->httpClient->request('POST', self::API_BASE_URL . '/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json; charset=utf-8',
                ],
                'json' => $payload,
                'timeout' => 30,
            ]);
            
            $statusCode = $response->getStatusCode();
            $content = $response->toArray();
            
            if ($statusCode !== 200) {
                throw new \Exception("OpenAI API error: " . ($content['error']['message'] ?? 'Unknown error'));
            }
            
            $this->logger->info('OpenAI API Response', [
                'status' => $statusCode,
                'usage' => $content['usage'] ?? null,
            ]);
            
            return $content;
            
        } catch (\Exception $e) {
            $this->logger->error('OpenAI API Error', [
                'message' => $e->getMessage(),
            ]);
            
            throw new \Exception('Erreur OpenAI: ' . $e->getMessage());
        }
    }
    
    public function extractContent(array $response): string
    {
        return $response['choices'][0]['message']['content'] ?? '';
    }
    
    public function createUserMessage(string $content): array
    {
        return ['role' => 'user', 'content' => $content];
    }
    
    public function createSystemMessage(string $content): array
    {
        return ['role' => 'system', 'content' => $content];
    }
    
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && $this->apiKey !== 'your_openai_api_key_here';
    }
    
    public function chatJson(array $messages, ?string $model = null): array
    {
        $response = $this->chat($messages, $model, [
            'response_format' => ['type' => 'json_object'],
        ]);
        
        $content = $this->extractContent($response);
        
        try {
            return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $this->logger->error('JSON parsing error', [
                'content' => $content,
                'error' => $e->getMessage(),
            ]);
            
            throw new \Exception('Erreur JSON');
        }
    }
}
