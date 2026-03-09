<?php

namespace App\Service\AI;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class SmartSuggestionsService
{
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;
    private string $openaiApiKey;
    private string $anthropicApiKey;

    public function __construct(
        HttpClientInterface $httpClient,
        LoggerInterface $logger,
        string $openaiApiKey,
        string $anthropicApiKey
    ) {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->openaiApiKey = $openaiApiKey;
        $this->anthropicApiKey = $anthropicApiKey;
    }

    public function generateTaskSuggestions(string $taskTitle, ?string $taskDescription = null): array
    {
        try {
            $prompt = "Task: {$taskTitle}\n";
            if ($taskDescription) {
                $prompt .= "Description: {$taskDescription}\n";
            }
            $prompt .= "Generate 5 actionable suggestions in JSON format: {\"suggestions\":[{\"title\":\"...\",\"description\":\"...\",\"priority\":\"high|medium|low\",\"estimated_time\":\"...\"}]}";

            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->openaiApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a project management expert. Return ONLY valid JSON.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 1000
                ]
            ]);

            $data = $response->toArray();
            $content = $data['choices'][0]['message']['content'] ?? '{}';
            $content = preg_replace('/```json\s*|\s*```/', '', $content);
            $result = json_decode(trim($content), true);

            return [
                'success' => true,
                'suggestions' => $result['suggestions'] ?? [],
                'provider' => 'openai',
                'timestamp' => new \DateTime()
            ];
        } catch (\Exception $e) {
            $this->logger->error('Task suggestions failed', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'suggestions' => [
                    ['title' => 'Break down the task', 'description' => 'Divide into smaller subtasks', 'priority' => 'high', 'estimated_time' => '30min']
                ],
                'provider' => 'fallback',
                'timestamp' => new \DateTime()
            ];
        }
    }

    public function generateProjectSuggestions(string $projectName, ?string $projectDescription = null, ?array $currentTasks = []): array
    {
        try {
            $prompt = "Project: {$projectName}\n";
            if ($projectDescription) {
                $prompt .= "Description: {$projectDescription}\n";
            }
            if (!empty($currentTasks)) {
                $prompt .= "Current tasks: " . implode(', ', $currentTasks) . "\n";
            }
            $prompt .= "Generate 5 project optimization suggestions in JSON: {\"suggestions\":[{\"type\":\"task|improvement|risk\",\"title\":\"...\",\"description\":\"...\",\"impact\":\"high|medium|low\"}]}";

            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->openaiApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a project consultant. Return ONLY valid JSON.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 1000
                ]
            ]);

            $data = $response->toArray();
            $content = $data['choices'][0]['message']['content'] ?? '{}';
            $content = preg_replace('/```json\s*|\s*```/', '', $content);
            $result = json_decode(trim($content), true);

            return [
                'success' => true,
                'suggestions' => $result['suggestions'] ?? [],
                'provider' => 'openai',
                'timestamp' => new \DateTime()
            ];
        } catch (\Exception $e) {
            $this->logger->error('Project suggestions failed', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'suggestions' => [
                    ['type' => 'task', 'title' => 'Create detailed plan', 'description' => 'Establish timeline with milestones', 'impact' => 'high']
                ],
                'provider' => 'fallback',
                'timestamp' => new \DateTime()
            ];
        }
    }

    public function generateDocumentIdeas(string $documentTitle, ?string $documentType = null): array
    {
        try {
            $prompt = "Document: {$documentTitle}\n";
            if ($documentType) {
                $prompt .= "Type: {$documentType}\n";
            }
            $prompt .= "Generate 5 creative content ideas in JSON: {\"ideas\":[{\"title\":\"...\",\"description\":\"...\",\"category\":\"...\"}]}";

            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->openaiApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a content creation expert. Return ONLY valid JSON.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 1000
                ]
            ]);

            $data = $response->toArray();
            $content = $data['choices'][0]['message']['content'] ?? '{}';
            $content = preg_replace('/```json\s*|\s*```/', '', $content);
            $result = json_decode(trim($content), true);

            return [
                'success' => true,
                'ideas' => $result['ideas'] ?? [],
                'provider' => 'openai',
                'timestamp' => new \DateTime()
            ];
        } catch (\Exception $e) {
            $this->logger->error('Document ideas failed', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'ideas' => [
                    ['title' => 'Clear introduction', 'description' => 'Start with executive summary', 'category' => 'structure']
                ],
                'provider' => 'fallback',
                'timestamp' => new \DateTime()
            ];
        }
    }
}
