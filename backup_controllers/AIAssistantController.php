<?php

namespace App\Controller;

use App\Entity\Project;
use App\Service\ChatbotService;
use App\Service\AI\SuggestionsService;
use App\Service\MatchingService;
use App\Service\SentimentAnalysisService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/ai', name: 'api_ai_')]
class AIAssistantController extends AbstractController
{
    public function __construct(
        private ChatbotService $chatbotService,
        private SuggestionsService $suggestionsService,
        private MatchingService $matchingService,
        private SentimentAnalysisService $sentimentService,
        private EntityManagerInterface $em
    ) {}

    #[Route('/chat', name: 'chat', methods: ['POST'])]
    public function chat(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $message = $data['message'] ?? '';

            if (empty($message)) {
                return $this->json(['error' => 'Message requis'], 400);
            }

            $conversationHistory = $data['history'] ?? [];
            $context = $data['context'] ?? [];
            
            $result = $this->chatbotService->chat($message, $conversationHistory, $context);
            
            return $this->json([
                'success' => $result['success'],
                'response' => $result['message'],
                'error' => $result['error']
            ]);
            
        } catch (\Exception $e) {
            error_log('❌ Erreur chat: ' . $e->getMessage());
            error_log('Stack: ' . $e->getTraceAsString());
            
            return $this->json([
                'success' => false,
                'response' => '',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/suggest-improvements/{projectId}', name: 'suggest_improvements', methods: ['POST'])]
    public function suggestImprovements(int $projectId): JsonResponse
    {
        try {
            $project = $this->em->getRepository(Project::class)->find($projectId);

            if (!$project) {
                return $this->json(['error' => 'Projet non trouvé'], 404);
            }

            $projectData = [
                'title' => $project->getTitle(),
                'description' => $project->getDescription(),
                'status' => $project->getStatus()
            ];

            $result = $this->suggestionService->suggestImprovements($project);

if (!$result['success']) {
    return $this->json([
        'success' => false,
        'error' => $result['error']
    ], 500);
}

$suggestions = $result['data'];


            return $this->json([
                'success' => true,
                'suggestions' => $suggestions
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/match-users/{projectId}', name: 'match_users', methods: ['POST'])]
    public function matchUsers(int $projectId): JsonResponse
    {
        try {
            $project = $this->em->getRepository(Project::class)->find($projectId);

            if (!$project) {
                return $this->json(['error' => 'Projet non trouvé'], 404);
            }

            $user = $this->getUser();
            if (!$user) {
                return $this->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            $projectData = [
                'required_skills' => $project->getRequiredSkills() ?? []
            ];

            $userData = [
                'skills' => $user->getSkills() ?? [],
                'interests' => $user->getInterests() ?? []
            ];

            $matchScore = $this->matchingService->calculateMatch($projectData, $userData);

            return $this->json([
                'success' => true,
                'match_score' => $matchScore
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/analyze-sentiment', name: 'analyze_sentiment', methods: ['POST'])]
    public function analyzeSentiment(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $text = $data['text'] ?? '';

            if (empty($text)) {
                return $this->json(['error' => 'Texte requis'], 400);
            }

            $score = $this->sentimentService->analyzeSentiment($text);

            return $this->json([
                'success' => true,
                'sentiment_score' => $score
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/enrich-profile', name: 'enrich_profile', methods: ['POST'])]
    public function enrichProfile(Request $request): JsonResponse
    {
        try {
            $user = $this->getUser();
            if (!$user) {
                return $this->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            return $this->json([
                'success' => true,
                'enriched_data' => [
                    'bio_suggestions' => 'Profil enrichi avec IA',
                    'skill_recommendations' => ['React', 'Vue.js', 'TypeScript']
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
