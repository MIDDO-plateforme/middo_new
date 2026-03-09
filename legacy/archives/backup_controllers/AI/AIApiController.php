<?php

namespace App\Controller\AI;

use App\Service\AI\SentimentAnalysisService;
use App\Service\AI\SmartSuggestionsService;
use App\Service\AI\MatchingService;
use App\Service\AI\PredictiveMetricsService;
use App\Service\AI\ChatbotService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/ai', name: 'api_ai_')]
#[IsGranted('ROLE_USER')]
class AIApiController extends AbstractController
{
    /**
     * 😊 Analyse de sentiment
     */
    #[Route('/sentiment', name: 'sentiment', methods: ['POST'])]
    public function analyzeSentiment(
        Request $request,
        SentimentAnalysisService $sentimentService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $text = $data['text'] ?? '';

        if (empty($text)) {
            return $this->json([
                'error' => 'Le texte est requis'
            ], 400);
        }

        $result = $sentimentService->analyzeSentiment($text);

        return $this->json($result);
    }

    /**
     * 💡 Suggestions pour tâche
     */
    #[Route('/suggestions/task', name: 'suggestions_task', methods: ['POST'])]
    public function suggestTask(
        Request $request,
        SmartSuggestionsService $suggestionsService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $title = $data['title'] ?? '';
        $description = $data['description'] ?? null;

        if (empty($title)) {
            return $this->json([
                'error' => 'Le titre de la tâche est requis'
            ], 400);
        }

        $result = $suggestionsService->generateTaskSuggestions($title, $description);

        return $this->json($result);
    }

    /**
     * 📊 Suggestions pour projet
     */
    #[Route('/suggestions/project', name: 'suggestions_project', methods: ['POST'])]
    public function suggestProject(
        Request $request,
        SmartSuggestionsService $suggestionsService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $name = $data['name'] ?? '';
        $description = $data['description'] ?? null;
        $currentTasks = $data['current_tasks'] ?? [];

        if (empty($name)) {
            return $this->json([
                'error' => 'Le nom du projet est requis'
            ], 400);
        }

        $result = $suggestionsService->generateProjectSuggestions($name, $description, $currentTasks);

        return $this->json($result);
    }

    /**
     * 📄 Idées pour document
     */
    #[Route('/suggestions/document', name: 'suggestions_document', methods: ['POST'])]
    public function suggestDocument(
        Request $request,
        SmartSuggestionsService $suggestionsService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $title = $data['title'] ?? '';
        $type = $data['type'] ?? null;

        if (empty($title)) {
            return $this->json([
                'error' => 'Le titre du document est requis'
            ], 400);
        }

        $result = $suggestionsService->generateDocumentIdeas($title, $type);

        return $this->json($result);
    }

    /**
     * 🤝 Matching collaborateurs pour projet
     */
    #[Route('/ia_engine/matching/collaborators', name: 'matching_collaborators', methods: ['POST'])]
    public function matchCollaborators(
        Request $request,
        MatchingService $matchingService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $projectRequirements = $data['project_requirements'] ?? [];
        $availableUsers = $data['available_users'] ?? [];
        $maxResults = $data['max_results'] ?? 5;

        if (empty($projectRequirements) || empty($availableUsers)) {
            return $this->json([
                'error' => 'Les exigences du projet et les utilisateurs disponibles sont requis'
            ], 400);
        }

        $result = $matchingService->matchCollaboratorsForProject(
            $projectRequirements,
            $availableUsers,
            $maxResults
        );

        return $this->json($result);
    }

    /**
     * 🎯 Suggestions de projets pour utilisateur
     */
    #[Route('/ia_engine/matching/projects', name: 'matching_projects', methods: ['POST'])]
    public function matchProjects(
        Request $request,
        MatchingService $matchingService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $userProfile = $data['user_profile'] ?? [];
        $availableProjects = $data['available_projects'] ?? [];
        $maxResults = $data['max_results'] ?? 5;

        if (empty($userProfile) || empty($availableProjects)) {
            return $this->json([
                'error' => 'Le profil utilisateur et les projets disponibles sont requis'
            ], 400);
        }

        $result = $matchingService->suggestProjectsForUser(
            $userProfile,
            $availableProjects,
            $maxResults
        );

        return $this->json($result);
    }

    /**
     * 💯 Score de compatibilité
     */
    #[Route('/ia_engine/matching/compatibility', name: 'matching_compatibility', methods: ['POST'])]
    public function calculateCompatibility(
        Request $request,
        MatchingService $matchingService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $user1Profile = $data['user1_profile'] ?? [];
        $user2Profile = $data['user2_profile'] ?? [];

        if (empty($user1Profile) || empty($user2Profile)) {
            return $this->json([
                'error' => 'Les profils des deux utilisateurs sont requis'
            ], 400);
        }

        $result = $matchingService->calculateCompatibilityScore($user1Profile, $user2Profile);

        return $this->json($result);
    }

    /**
     * 🔍 Analyse des gaps de compétences
     */
    #[Route('/ia_engine/matching/skill-gaps', name: 'matching_skill_gaps', methods: ['POST'])]
    public function identifySkillGaps(
        Request $request,
        MatchingService $matchingService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $teamMembers = $data['team_members'] ?? [];
        $projectRequirements = $data['project_requirements'] ?? [];

        if (empty($teamMembers) || empty($projectRequirements)) {
            return $this->json([
                'error' => 'Les membres de l\'équipe et les exigences du projet sont requis'
            ], 400);
        }

        $result = $matchingService->identifySkillGaps($teamMembers, $projectRequirements);

        return $this->json($result);
    }

    /**
     * 📅 Prédiction de fin de projet
     */
    #[Route('/predictions/completion', name: 'predictions_completion', methods: ['POST'])]
    public function predictCompletion(
        Request $request,
        PredictiveMetricsService $predictiveService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $projectData = $data['project_data'] ?? [];

        if (empty($projectData)) {
            return $this->json([
                'error' => 'Les données du projet sont requises'
            ], 400);
        }

        $result = $predictiveService->predictProjectCompletion($projectData);

        return $this->json($result);
    }

    /**
     * 💰 Prédiction budgétaire
     */
    #[Route('/predictions/budget', name: 'predictions_budget', methods: ['POST'])]
    public function predictBudget(
        Request $request,
        PredictiveMetricsService $predictiveService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $projectFinancials = $data['project_financials'] ?? [];

        if (empty($projectFinancials)) {
            return $this->json([
                'error' => 'Les données financières sont requises'
            ], 400);
        }

        $result = $predictiveService->predictBudgetOverrun($projectFinancials);

        return $this->json($result);
    }

    /**
     * ⚠️ Analyse des risques
     */
    #[Route('/predictions/risks', name: 'predictions_risks', methods: ['POST'])]
    public function analyzeRisks(
        Request $request,
        PredictiveMetricsService $predictiveService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $projectMetrics = $data['project_metrics'] ?? [];

        if (empty($projectMetrics)) {
            return $this->json([
                'error' => 'Les métriques du projet sont requises'
            ], 400);
        }

        $result = $predictiveService->analyzeProjectRisks($projectMetrics);

        return $this->json($result);
    }

    /**
     * 📈 Calcul de vélocité d'équipe
     */
    #[Route('/predictions/velocity', name: 'predictions_velocity', methods: ['POST'])]
    public function calculateVelocity(
        Request $request,
        PredictiveMetricsService $predictiveService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $historicalData = $data['historical_data'] ?? [];

        if (empty($historicalData)) {
            return $this->json([
                'error' => 'Les données historiques sont requises'
            ], 400);
        }

        $result = $predictiveService->calculateTeamVelocity($historicalData);

        return $this->json($result);
    }

    /**
     * 💬 Chatbot conversationnel
     */
    #[Route('/chatbot/message', name: 'chatbot_message', methods: ['POST'])]
    public function chatbotMessage(
        Request $request,
        ChatbotService $chatbotService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $message = $data['message'] ?? '';
        $userContext = $data['context'] ?? null;
        $sessionId = $data['session_id'] ?? null;

        if (empty($message)) {
            return $this->json([
                'error' => 'Le message est requis'
            ], 400);
        }

        $result = $chatbotService->chat($message, $userContext, $sessionId);

        return $this->json($result);
    }

    /**
     * 🧠 Analyse d'intention
     */
    #[Route('/chatbot/intent', name: 'chatbot_intent', methods: ['POST'])]
    public function analyzeIntent(
        Request $request,
        ChatbotService $chatbotService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $message = $data['message'] ?? '';

        if (empty($message)) {
            return $this->json([
                'error' => 'Le message est requis'
            ], 400);
        }

        $result = $chatbotService->analyzeIntent($message);

        return $this->json($result);
    }

    /**
     * 📚 Suggestions contextuelles
     */
    #[Route('/chatbot/suggestions', name: 'chatbot_suggestions', methods: ['POST'])]
    public function contextualSuggestions(
        Request $request,
        ChatbotService $chatbotService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $userContext = $data['context'] ?? null;

        $result = $chatbotService->generateContextualSuggestions($userContext);

        return $this->json($result);
    }

    /**
     * 🔍 Recherche intelligente
     */
    #[Route('/chatbot/search', name: 'chatbot_search', methods: ['POST'])]
    public function intelligentSearch(
        Request $request,
        ChatbotService $chatbotService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $query = $data['query'] ?? '';
        $userContext = $data['context'] ?? null;

        if (empty($query)) {
            return $this->json([
                'error' => 'La requête de recherche est requise'
            ], 400);
        }

        $result = $chatbotService->intelligentSearch($query, $userContext);

        return $this->json($result);
    }

    /**
     * 🔄 Réinitialisation de conversation
     */
    #[Route('/chatbot/reset', name: 'chatbot_reset', methods: ['POST'])]
    public function resetConversation(
        Request $request,
        ChatbotService $chatbotService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $sessionId = $data['session_id'] ?? null;

        $chatbotService->resetConversation($sessionId);

        return $this->json([
            'success' => true,
            'message' => 'Conversation réinitialisée'
        ]);
    }
}
