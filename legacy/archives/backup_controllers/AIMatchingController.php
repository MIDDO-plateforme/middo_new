<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

#[Route('/api/matching')]
class AIMatchingController extends AbstractController
{
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;
    private string $openaiApiKey;

    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->openaiApiKey = $_ENV['OPENAI_API_KEY'] ?? '';
    }

    #[Route('/compatibility', name: 'api_matching_compatibility', methods: ['POST'])]
    public function projectCompatibility(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            $userSkills = $data['userSkills'] ?? [];
            $projectRequirements = $data['projectRequirements'] ?? [];
            
            if (empty($userSkills) || empty($projectRequirements)) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'Skills and requirements are required'
                ], 400);
            }
            
            $prompt = "Analyze compatibility between user skills and project requirements.

User Skills: " . implode(', ', $userSkills) . "
Project Requirements: " . implode(', ', $projectRequirements) . "

Provide a JSON response with:
- compatibility_score (0-100)
- matching_skills (array of skills that match)
- skills_to_learn (array of missing skills)
- strengths (string describing what the user does well)
- challenges (string describing potential difficulties)
- recommendation (string with advice)";
            
            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->openaiApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a technical skills analyzer. Always respond with valid JSON.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.7,
                ]
            ]);
            
            $result = json_decode($response->getContent(), true);
            $content = $result['choices'][0]['message']['content'] ?? '{}';
            
            if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
                $content = $matches[1];
            }
            
            $analysis = json_decode($content, true);
            
            return new JsonResponse([
                'success' => true,
                'analysis' => $analysis,
                'provider' => 'openai'
            ]);
            
        } catch (\Exception $e) {
            $this->logger->error('Compatibility analysis error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'error' => 'Analysis failed'
            ], 500);
        }
    }

    #[Route('/skills', name: 'api_matching_skills', methods: ['POST'])]
    public function skillDevelopment(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            $currentSkills = $data['currentSkills'] ?? [];
            $targetRole = $data['targetRole'] ?? '';
            $industryStandards = $data['industryStandards'] ?? [];
            
            if (empty($currentSkills) || empty($targetRole)) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'Current skills and target role are required'
                ], 400);
            }
            
            $prompt = "Analyze skill gap and create learning roadmap.

Current Skills: " . implode(', ', $currentSkills) . "
Target Role: " . $targetRole . "
Industry Standards: " . implode(', ', $industryStandards) . "

Provide a JSON response with:
- target_role (string)
- coverage (0-100, percentage of required skills already possessed)
- current_skills (array)
- skills_to_learn (array of missing skills)
- learning_roadmap (array of objects with: skill, priority (high/medium/low), reason, estimated_time)";
            
            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->openaiApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a career development advisor. Always respond with valid JSON.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.7,
                ]
            ]);
            
            $result = json_decode($response->getContent(), true);
            $content = $result['choices'][0]['message']['content'] ?? '{}';
            
            if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
                $content = $matches[1];
            }
            
            $analysis = json_decode($content, true);
            
            return new JsonResponse([
                'success' => true,
                'analysis' => $analysis,
                'provider' => 'openai'
            ]);
            
        } catch (\Exception $e) {
            $this->logger->error('Skill development error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'error' => 'Analysis failed'
            ], 500);
        }
    }

    #[Route('/collaborators', name: 'api_matching_collaborators', methods: ['POST'])]
    public function findCollaborators(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            $requiredSkills = $data['requiredSkills'] ?? [];
            $optionalSkills = $data['optionalSkills'] ?? [];
            $minimumAvailability = $data['minimumAvailability'] ?? 0;
            $maxResults = $data['maxResults'] ?? 5;
            
            if (empty($requiredSkills)) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'Required skills are mandatory'
                ], 400);
            }
            
            $prompt = "Find and suggest potential collaborators based on requirements.

Required Skills: " . implode(', ', $requiredSkills) . "
Optional Skills: " . implode(', ', $optionalSkills) . "
Minimum Availability: " . $minimumAvailability . "%
Max Results: " . $maxResults . "

Provide a JSON response with an array of 'matches' (exactly " . $maxResults . " collaborators), each with:
- user_id (number, unique ID between 100-200)
- role (string, e.g., 'Backend Developer', 'Full Stack Developer')
- match_score (0-100, based on required skills match)
- matching_skills (array of skills that match requirements)
- strengths (string, 1-2 sentences describing main strengths)
- why (string, 1-2 sentences explaining why they're a good match)";
            
            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->openaiApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a talent matching specialist. Always respond with valid JSON containing exactly the requested number of collaborators.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.8,
                ]
            ]);
            
            $result = json_decode($response->getContent(), true);
            $content = $result['choices'][0]['message']['content'] ?? '{}';
            
            if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
                $content = $matches[1];
            }
            
            $data = json_decode($content, true);
            
            return new JsonResponse([
                'success' => true,
                'matches' => $data['matches'] ?? [],
                'provider' => 'openai'
            ]);
            
        } catch (\Exception $e) {
            $this->logger->error('Find collaborators error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'error' => 'Search failed'
            ], 500);
        }
    }
}
