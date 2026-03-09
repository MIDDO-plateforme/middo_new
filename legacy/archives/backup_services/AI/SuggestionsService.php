<?php

namespace App\Service\AI;

use App\Entity\Project;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SuggestionsService
{
    private string $claudeApiKey;
    private string $claudeBaseUrl = 'https://api.anthropic.com/v1/messages';
    
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        string $claudeApiKey
    ) {
        $this->claudeApiKey = $claudeApiKey;
    }

    public function getSuggestionsForProject(Project $project): array
    {
        try {
            $requiredSkills = $this->analyzeProjectSkills($project);
            $workspaceMembers = $this->getWorkspaceMembers($project->getWorkspace());
            $suggestions = $this->calculateMemberScores($requiredSkills, $workspaceMembers);
            $topSuggestions = $this->getTopSuggestions($suggestions, 5);
            
            return [
                'success' => true,
                'required_skills' => $requiredSkills,
                'suggestions' => $topSuggestions,
                'total_members' => count($workspaceMembers)
            ];
            
        } catch (\Exception $e) {
            $this->logger->error('AI suggestions failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage(), 'suggestions' => []];
        }
    }

    private function analyzeProjectSkills(Project $project): array
    {
        $prompt = <<<PROMPT
Tu es expert en matching d'équipes tech.
Analyse ce projet et extrais les compétences requises en JSON.

PROJET:
Nom: "{$project->getName()}"
Description: "{$project->getDescription()}"

Retourne UNIQUEMENT ce JSON:
{
    "skills": [
        {"name": "PHP", "importance": 95},
        {"name": "Symfony", "importance": 90}
    ]
}
PROMPT;

        $response = $this->httpClient->request('POST', $this->claudeBaseUrl, [
            'headers' => [
                'Content-Type' => 'application/json',
                'x-api-key' => $this->claudeApiKey,
                'anthropic-version' => '2023-06-01'
            ],
            'json' => [
                'model' => 'claude-3-5-sonnet-20241022',
                'max_tokens' => 1024,
                'messages' => [['role' => 'user', 'content' => $prompt]]
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Claude API error');
        }

        $data = $response->toArray();
        $text = $data['content'][0]['text'] ?? '{}';
        
        $parsed = json_decode(trim(preg_replace('/^```json\s*|\s*```$/m', '', $text)), true);
        
        return $parsed ?? ['skills' => []];
    }

    private function getWorkspaceMembers($workspace): array
    {
        return array_map(fn($user) => [
            'id' => $user->getId(),
            'name' => $user->getEmail(),
            'skills' => [['name' => 'PHP'], ['name' => 'JavaScript']]
        ], $workspace->getMembers()->toArray());
    }

    private function calculateMemberScores(array $requiredSkills, array $members): array
    {
        $suggestions = [];
        foreach ($members as $member) {
            $score = $this->calculateScore($requiredSkills['skills'] ?? [], $member['skills']);
            if ($score > 0) {
                $suggestions[] = [
                    'user_id' => $member['id'],
                    'user' => $member['name'],
                    'score' => $score,
                    'matched_skills' => array_column($member['skills'], 'name')
                ];
            }
        }
        return $suggestions;
    }

    private function calculateScore(array $required, array $userSkills): int
    {
        $totalImportance = array_sum(array_column($required, 'importance'));
        $matched = 0;
        foreach ($required as $req) {
            foreach ($userSkills as $us) {
                if (stripos($us['name'], $req['name']) !== false) {
                    $matched += $req['importance'];
                    break;
                }
            }
        }
        return $totalImportance > 0 ? (int)(($matched / $totalImportance) * 100) : 0;
    }

    private function getTopSuggestions(array $suggestions, int $limit): array
    {
        usort($suggestions, fn($a, $b) => $b['score'] <=> $a['score']);
        return array_slice($suggestions, 0, $limit);
    }
}