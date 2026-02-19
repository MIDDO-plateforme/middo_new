<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;

/**
 * Contrôleur API pour les Suggestions IA
 * 
 * @package App\Controller\Api
 * @author MIDDO Team
 * @version 2.0 - Optimisé Session 24
 */
class SuggestionsController extends AbstractController
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
     * Endpoint principal des suggestions
     * 
     * @Route("/api/suggestions", name="api_suggestions", methods={"POST"})
     * 
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/suggestions', name: 'api_suggestions', methods: ['POST'])]
    public function getSuggestions(Request $request): JsonResponse
    {
        $this->logger->info('SuggestionsController: Nouvelle requête de suggestions');

        try {
            // Récupérer les données de la requête
            $data = json_decode($request->getContent(), true);

            // Valider les données
            if (!isset($data['context']) || empty(trim($data['context']))) {
                $this->logger->warning('SuggestionsController: Contexte manquant ou vide');
                return $this->json([
                    'success' => false,
                    'error' => 'Le contexte est requis pour générer des suggestions'
                ], 400);
            }

            $context = trim($data['context']);
            $limit = isset($data['limit']) ? (int)$data['limit'] : 5;

            // Valider la limite
            if ($limit < 1 || $limit > 20) {
                $this->logger->warning('SuggestionsController: Limite invalide', [
                    'limit' => $limit
                ]);
                return $this->json([
                    'success' => false,
                    'error' => 'La limite doit être entre 1 et 20'
                ], 400);
            }

            $this->logger->info('SuggestionsController: Génération de suggestions', [
                'context_length' => strlen($context),
                'limit' => $limit
            ]);

            // Récupérer la clé OpenAI
            $openaiApiKey = $_ENV['OPENAI_API_KEY'] ?? null;

            if (!$openaiApiKey) {
                $this->logger->error('SuggestionsController: Clé OpenAI manquante');
                return $this->json([
                    'success' => false,
                    'error' => 'Configuration API manquante'
                ], 500);
            }

            // Générer les suggestions
            $suggestions = $this->generateSuggestions($context, $limit, $openaiApiKey);

            $this->logger->info('SuggestionsController: Suggestions générées avec succès', [
                'count' => count($suggestions)
            ]);

            return $this->json([
                'success' => true,
                'suggestions' => $suggestions,
                'count' => count($suggestions),
                'timestamp' => time()
            ]);

        } catch (\JsonException $e) {
            $this->logger->error('SuggestionsController: Erreur JSON', [
                'error' => $e->getMessage()
            ]);
            return $this->json([
                'success' => false,
                'error' => 'Format JSON invalide'
            ], 400);

        } catch (\Exception $e) {
            $this->logger->error('SuggestionsController: Erreur lors du traitement', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->json([
                'success' => false,
                'error' => 'Une erreur est survenue lors de la génération des suggestions'
            ], 500);
        }
    }

    /**
     * Endpoint de test des suggestions
     * 
     * @Route("/api/suggestions/test", name="api_suggestions_test", methods={"GET"})
     */
    #[Route('/api/suggestions/test', name: 'api_suggestions_test', methods: ['GET'])]
    public function test(): JsonResponse
    {
        $this->logger->info('SuggestionsController: Test endpoint appelé');

        return $this->json([
            'success' => true,
            'message' => 'Suggestions API opérationnel',
            'version' => '2.0',
            'status' => 'online',
            'features' => [
                'routes' => 'enabled',
                'logging' => 'enabled',
                'error_handling' => 'enabled',
                'validation' => 'enhanced',
                'openai' => isset($_ENV['OPENAI_API_KEY']) ? 'configured' : 'missing'
            ],
            'limits' => [
                'min' => 1,
                'max' => 20,
                'default' => 5
            ]
        ]);
    }

    /**
     * Génération des suggestions via IA
     * 
     * @param string $context
     * @param int $limit
     * @param string $apiKey
     * @return array
     */
    private function generateSuggestions(string $context, int $limit, string $apiKey): array
    {
        // TODO: Implémenter l'appel réel à OpenAI GPT
        // Pour l'instant, retourne des suggestions simulées
        
        $this->logger->info('SuggestionsController: Simulation génération suggestions');

        $suggestions = [];
        
        for ($i = 1; $i <= $limit; $i++) {
            $suggestions[] = [
                'id' => $i,
                'text' => "Suggestion {$i} basée sur: \"{$context}\"",
                'relevance' => round(mt_rand(70, 100) / 100, 2),
                'type' => ['action', 'question', 'resource'][mt_rand(0, 2)]
            ];
        }

        return $suggestions;
    }
}
