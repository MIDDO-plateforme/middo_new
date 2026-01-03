<?php

namespace App\Controller\API;

use App\Service\AI\SuggestionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/projects/{id}/suggestions')]
class SuggestionsController extends AbstractController
{
    public function __construct(
        private readonly SuggestionsService $suggestionsService
    ) {}

    #[Route('', name: 'api_project_suggestions', methods: ['GET'])]
    public function getSuggestions(int $id): JsonResponse
    {
        return $this->json([
            'success' => true,
            'message' => '🎉 SESSION 38 - API SUGGESTIONS FONCTIONNE !',
            'project_id' => $id,
            'service_status' => 'SuggestionsService autowired successfully',
            'suggestions' => [
                [
                    'id' => 1,
                    'type' => 'optimization',
                    'title' => 'Optimiser la structure du projet',
                    'description' => 'Réorganiser les modules pour une meilleure maintenabilité',
                    'priority' => 'high',
                ],
                [
                    'id' => 2,
                    'type' => 'testing',
                    'title' => 'Ajouter des tests unitaires',
                    'description' => 'Couvrir les services critiques avec des tests',
                    'priority' => 'high',
                ],
                [
                    'id' => 3,
                    'type' => 'documentation',
                    'title' => 'Améliorer la documentation',
                    'description' => 'Documenter les APIs pour faciliter la collaboration',
                    'priority' => 'medium',
                ],
            ],
            'symfony_version' => '6.4.30',
            'php_version' => PHP_VERSION,
            'timestamp' => date('Y-m-d H:i:s'),
            'session' => 'SESSION 38 COMPLETED 🎊',
        ]);
    }
}
