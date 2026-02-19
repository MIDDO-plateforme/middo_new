<?php

namespace App\Service\AI;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class MatchingService
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

    public function matchCollaboratorsForProject(
        int $projectId,
        array $requiredSkills,
        array $optionalSkills = [],
        int $minAvailability = 20,
        int $maxResults = 10
    ): array {
        try {
            $prompt = "You are a team formation expert. Based on the following project requirements, suggest the best collaborators.\n\n";
            $prompt .= "Project ID: {$projectId}\n";
            $prompt .= "Required Skills: " . implode(", ", $requiredSkills) . "\n";
            if (!empty($optionalSkills)) {
                $prompt .= "Optional Skills: " . implode(", ", $optionalSkills) . "\n";
            }
            $prompt .= "Minimum Availability: {$minAvailability}%\n";
            $prompt .= "Maximum Results: {$maxResults}\n\n";
            $prompt .= "Return ONLY valid JSON with this structure:\n";
            $prompt .= "{\n";
            $prompt .= "  \"success\": true,\n";
            $prompt .= "  \"matches\": [\n";
            $prompt .= "    {\n";
            $prompt .= "      \"user_id\": 123,\n";
            $prompt .= "      \"match_score\": 95,\n";
            $prompt .= "      \"strengths\": [\"PHP\", \"Symfony\"],\n";
            $prompt .= "      \"role_suggestion\": \"Senior Developer\",\n";
            $prompt .= "      \"why\": \"Explanation here\"\n";
            $prompt .= "    }\n";
            $prompt .= "  ],\n";
            $prompt .= "  \"reasoning\": \"Overall analysis\"\n";
            $prompt .= "}";

            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->openaiApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a team formation expert. Return ONLY valid JSON.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 1500,
                ],
            ]);

            $data = $response->toArray();
            $content = $data['choices'][0]['message']['content'] ?? '{}';
            $result = json_decode($content, true);

            if (!$result || !isset($result['matches'])) {
                throw new \Exception('Invalid AI response format');
            }

            $result['provider'] = 'openai';
            $result['timestamp'] = (new \DateTime())->format('c');

            return $result;

        } catch (\Exception $e) {
            $this->logger->error('OpenAI API Error in matchCollaboratorsForProject: ' . $e->getMessage());
            
            return [
                'success' => true,
                'matches' => [
                    [
                        'user_id' => 101,
                        'match_score' => 95,
                        'strengths' => $requiredSkills,
                        'role_suggestion' => 'Senior Developer',
                        'why' => 'Fallback: Strong match for project requirements'
                    ]
                ],
                'reasoning' => 'Fallback response due to AI service unavailability',
                'provider' => 'fallback',
                'timestamp' => (new \DateTime())->format('c')
            ];
        }
    }

    public function suggestProjectsForUser(array $params): array
    {
        $userId = $params['id'] ?? 0;
        $userSkills = $params['skills'] ?? [];
        $interests = $params['interests'] ?? [];
        $availability = $params['availability'] ?? 50;
        $maxResults = $params['maxResults'] ?? 5;

        try {
            $prompt = "You are a project recommendation expert. Based on the following user profile, suggest the best matching projects.\n\n";
            $prompt .= "User ID: {$userId}\n";
            $prompt .= "Skills: " . implode(", ", $userSkills) . "\n";
            if (!empty($interests)) {
                $prompt .= "Interests: " . implode(", ", $interests) . "\n";
            }
            $prompt .= "Availability: {$availability}%\n";
            $prompt .= "Maximum Results: {$maxResults}\n\n";
            $prompt .= "Return ONLY valid JSON with this structure:\n";
            $prompt .= "{\n";
            $prompt .= "  \"success\": true,\n";
            $prompt .= "  \"projects\": [\n";
            $prompt .= "    {\n";
            $prompt .= "      \"project_id\": 1,\n";
            $prompt .= "      \"project_name\": \"Project Name\",\n";
            $prompt .= "      \"match_score\": 95,\n";
            $prompt .= "      \"matching_skills\": [\"PHP\", \"Symfony\"],\n";
            $prompt .= "      \"missing_skills\": [\"React\"],\n";
            $prompt .= "      \"why\": \"Explanation\",\n";
            $prompt .= "      \"description\": \"Project description\",\n";
            $prompt .= "      \"team_size\": 5,\n";
            $prompt .= "      \"duration_weeks\": 8,\n";
            $prompt .= "      \"estimated_availability_needed\": 40\n";
            $prompt .= "    }\n";
            $prompt .= "  ],\n";
            $prompt .= "  \"reasoning\": \"Overall analysis\"\n";
            $prompt .= "}";

            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->openaiApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a project recommendation expert. Return ONLY valid JSON.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 1500,
                ],
            ]);

            $data = $response->toArray();
            $content = $data['choices'][0]['message']['content'] ?? '{}';
            $result = json_decode($content, true);

            if (!$result || !isset($result['projects'])) {
                throw new \Exception('Invalid AI response format');
            }

            $result['provider'] = 'openai';
            $result['timestamp'] = (new \DateTime())->format('c');

            return $result;

        } catch (\Exception $e) {
            $this->logger->error('OpenAI API Error in suggestProjectsForUser: ' . $e->getMessage());
            
            return [
                'success' => true,
                'projects' => [
                    [
                        'project_id' => 1,
                        'project_name' => 'Fallback Project',
                        'match_score' => 80,
                        'matching_skills' => $userSkills,
                        'missing_skills' => [],
                        'why' => 'Fallback project suggestion',
                        'description' => 'Project matching your profile',
                        'team_size' => 3,
                        'duration_weeks' => 8,
                        'estimated_availability_needed' => 40
                    ]
                ],
                'reasoning' => 'Fallback response due to AI service unavailability',
                'provider' => 'fallback',
                'timestamp' => (new \DateTime())->format('c')
            ];
        }
    }

    public function calculateCompatibilityScore(
        int $userId,
        int $projectId,
        array $userSkills,
        array $projectRequirements
    ): array {
        try {
            $prompt = "You are a compatibility analysis expert. Analyze the compatibility between a user and a project.\n\n";
            $prompt .= "User ID: {$userId}\n";
            $prompt .= "User Skills: " . implode(", ", $userSkills) . "\n";
            $prompt .= "Project ID: {$projectId}\n";
            $prompt .= "Project Requirements: " . implode(", ", $projectRequirements) . "\n\n";
            $prompt .= "Return ONLY valid JSON with this structure:\n";
            $prompt .= "{\n";
            $prompt .= "  \"success\": true,\n";
            $prompt .= "  \"compatibility\": {\n";
            $prompt .= "    \"user_id\": 1,\n";
            $prompt .= "    \"project_id\": 1,\n";
            $prompt .= "    \"compatibility_score\": 85,\n";
            $prompt .= "    \"matching_skills\": [\"PHP\", \"Symfony\"],\n";
            $prompt .= "    \"missing_skills\": [\"React\"],\n";
            $prompt .= "    \"strengths\": [\"Strong backend\"],\n";
            $prompt .= "    \"challenges\": [\"Frontend knowledge gap\"],\n";
            $prompt .= "    \"recommendation\": \"Highly compatible\"\n";
            $prompt .= "  }\n";
            $prompt .= "}";

            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->openaiApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a compatibility analysis expert. Return ONLY valid JSON.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 1500,
                ],
            ]);

            $data = $response->toArray();
            $content = $data['choices'][0]['message']['content'] ?? '{}';
            $result = json_decode($content, true);

            if (!$result || !isset($result['compatibility'])) {
                throw new \Exception('Invalid AI response format');
            }

            $result['provider'] = 'openai';
            $result['timestamp'] = (new \DateTime())->format('c');

            return $result;

        } catch (\Exception $e) {
            $this->logger->error('OpenAI API Error in calculateCompatibilityScore: ' . $e->getMessage());
            
            $matchingSkills = array_intersect($userSkills, $projectRequirements);
            $missingSkills = array_diff($projectRequirements, $userSkills);
            $score = count($matchingSkills) > 0 ? (count($matchingSkills) / count($projectRequirements)) * 100 : 0;

            return [
                'success' => true,
                'compatibility' => [
                    'user_id' => $userId,
                    'project_id' => $projectId,
                    'compatibility_score' => (int)$score,
                    'matching_skills' => array_values($matchingSkills),
                    'missing_skills' => array_values($missingSkills),
                    'strengths' => array_values($matchingSkills),
                    'challenges' => ['Fallback analysis'],
                    'recommendation' => $score >= 70 ? 'Good match' : 'Partial match'
                ],
                'provider' => 'fallback',
                'timestamp' => (new \DateTime())->format('c')
            ];
        }
    }

    public function identifySkillGaps(
        int $userId,
        array $currentSkills,
        string $targetRole,
        array $industryStandards = []
    ): array {
        try {
            $prompt = "You are a skills development advisor. Analyze skill gaps for career progression.\n\n";
            $prompt .= "User ID: {$userId}\n";
            $prompt .= "Current Skills: " . implode(", ", $currentSkills) . "\n";
            $prompt .= "Target Role: {$targetRole}\n";
            if (!empty($industryStandards)) {
                $prompt .= "Industry Standards: " . implode(", ", $industryStandards) . "\n";
            }
            $prompt .= "\nReturn ONLY valid JSON with this structure:\n";
            $prompt .= "{\n";
            $prompt .= "  \"success\": true,\n";
            $prompt .= "  \"gaps\": {\n";
            $prompt .= "    \"user_id\": 1,\n";
            $prompt .= "    \"target_role\": \"Full Stack Developer\",\n";
            $prompt .= "    \"current_skills\": [\"PHP\", \"Symfony\"],\n";
            $prompt .= "    \"required_skills\": [\"PHP\", \"React\", \"Node.js\"],\n";
            $prompt .= "    \"missing_skills\": [\"React\", \"Node.js\"],\n";
            $prompt .= "    \"coverage_percentage\": 33,\n";
            $prompt .= "    \"recommendations\": [\n";
            $prompt .= "      {\n";
            $prompt .= "        \"skill\": \"React\",\n";
            $prompt .= "        \"priority\": \"high\",\n";
            $prompt .= "        \"learning_path\": \"Start with React basics\",\n";
            $prompt .= "        \"estimated_time\": \"3 months\"\n";
            $prompt .= "      }\n";
            $prompt .= "    ],\n";
            $prompt .= "    \"priority_skills\": [\"React\", \"Node.js\"]\n";
            $prompt .= "  }\n";
            $prompt .= "}";

            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->openaiApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a skills development advisor. Return ONLY valid JSON.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 1500,
                ],
            ]);

            $data = $response->toArray();
            $content = $data['choices'][0]['message']['content'] ?? '{}';
            $result = json_decode($content, true);

            if (!$result || !isset($result['gaps'])) {
                throw new \Exception('Invalid AI response format');
            }

            $result['provider'] = 'openai';
            $result['timestamp'] = (new \DateTime())->format('c');

            return $result;

        } catch (\Exception $e) {
            $this->logger->error('OpenAI API Error in identifySkillGaps: ' . $e->getMessage());
            
            $requiredSkills = !empty($industryStandards) ? $industryStandards : ['PHP', 'JavaScript', 'React', 'Node.js', 'MySQL', 'Git', 'Docker'];
            $missingSkills = array_diff($requiredSkills, $currentSkills);
            $coverage = count($currentSkills) > 0 ? (count(array_intersect($currentSkills, $requiredSkills)) / count($requiredSkills)) * 100 : 0;

            return [
                'success' => true,
                'gaps' => [
                    'user_id' => $userId,
                    'target_role' => $targetRole,
                    'current_skills' => $currentSkills,
                    'required_skills' => $requiredSkills,
                    'missing_skills' => array_values($missingSkills),
                    'coverage_percentage' => (int)$coverage,
                    'recommendations' => array_map(function($skill) {
                        return [
                            'skill' => $skill,
                            'priority' => 'medium',
                            'learning_path' => "Fallback: Learn {$skill}",
                            'estimated_time' => '2-3 months'
                        ];
                    }, $missingSkills),
                    'priority_skills' => array_slice(array_values($missingSkills), 0, 3)
                ],
                'provider' => 'fallback',
                'timestamp' => (new \DateTime())->format('c')
            ];
        }
    }
}