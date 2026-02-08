<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;

/**
 * Contrôleur API pour le Chatbot IA
 * 
 * @package App\Controller\Api
 * @author MIDDO Team
 * @version 2.0 - Optimisé Session 24
 */
class ChatbotController extends AbstractController
{
    private LoggerInterface $logger;

    /**
     * Constructeur avec injection de dépendances
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Endpoint principal du chatbot
     * 
     * @Route("/api/chatbot", name="api_chatbot", methods={"POST"})
     * 
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/chatbot', name: 'api_chatbot', methods: ['POST'])]
    public function chat(Request $request): JsonResponse
    {
        $this->logger->info('ChatbotController: Nouvelle requête reçue');

        try {
            // Récupérer les données de la requête
            $data = json_decode($request->getContent(), true);

            // Valider les données
            if (!isset($data['message']) || empty(trim($data['message']))) {
                $this->logger->warning('ChatbotController: Message manquant ou vide');
                return $this->json([
                    'success' => false,
                    'error' => 'Le message est requis'
                ], 400);
            }

            $userMessage = trim($data['message']);
            $this->logger->info('ChatbotController: Message utilisateur', [
                'length' => strlen($userMessage)
            ]);

            // Récupérer la clé OpenAI depuis les variables d'environnement
            $openaiApiKey = $_ENV['OPENAI_API_KEY'] ?? null;

            if (!$openaiApiKey) {
                $this->logger->error('ChatbotController: Clé OpenAI manquante');
                return $this->json([
                    'success' => false,
                    'error' => 'Configuration API manquante'
                ], 500);
            }

            // Appeler l'API OpenAI (simulation pour l'instant)
            // TODO: Implémenter l'appel réel à OpenAI
            $response = $this->callOpenAI($userMessage, $openaiApiKey);

            $this->logger->info('ChatbotController: Réponse générée avec succès');

            return $this->json([
                'success' => true,
                'response' => $response,
                'timestamp' => time()
            ]);

        } catch (\Exception $e) {
            $this->logger->error('ChatbotController: Erreur lors du traitement', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->json([
                'success' => false,
                'error' => 'Une erreur est survenue lors du traitement de votre message'
            ], 500);
        }
    }

    /**
     * Endpoint de test du chatbot
     * 
     * @Route("/api/chatbot/test", name="api_chatbot_test", methods={"GET"})
     */
    #[Route('/api/chatbot/test', name: 'api_chatbot_test', methods: ['GET'])]
    public function test(): JsonResponse
    {
        $this->logger->info('ChatbotController: Test endpoint appelé');

        return $this->json([
            'success' => true,
            'message' => 'Chatbot API opérationnel',
            'version' => '2.0',
            'status' => 'online',
            'features' => [
                'routes' => 'enabled',
                'logging' => 'enabled',
                'error_handling' => 'enabled',
                'openai' => isset($_ENV['OPENAI_API_KEY']) ? 'configured' : 'missing'
            ]
        ]);
    }

    /**
     * Appel à l'API OpenAI
     * 
     * @param string $message
     * @param string $apiKey
     * @return string
     */
    private function callOpenAI(string $message, string $apiKey): string
    {
        // TODO: Implémenter l'appel réel à OpenAI GPT
        // Pour l'instant, retourne une réponse simulée
        
        $this->logger->info('ChatbotController: Simulation appel OpenAI');

        // Simulation de réponse
        return "Je suis le chatbot MIDDO (version optimisée). Vous avez dit : \"$message\". "
             . "L'intégration complète avec OpenAI GPT sera implémentée prochainement.";
    }
}
