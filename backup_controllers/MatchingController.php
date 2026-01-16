<?php

namespace App\Controller;

use App\Service\MatchingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MatchingController extends AbstractController
{
    private $matchingService;

    public function __construct(MatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    #[Route('/api/matching/find', name: 'api_matching_find', methods: ['POST'])]
    public function findMatches(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $userProfile = [
            'userId' => $data['userId'] ?? 1,
            'skills' => $data['skills'] ?? ['Symfony', 'React', 'Blockchain', 'PHP'],
            'budgetMin' => $data['budgetMin'] ?? 1000,
            'location' => $data['location'] ?? 'Remote',
        ];

        $matches = $this->matchingService->findMatches($userProfile);

        return $this->json([
            'success' => true,
            'matches' => $matches,
            'count' => count($matches),
            'message' => count($matches) . ' missions trouvées',
        ]);
    }

    #[Route('/api/matching/mission/{id}', name: 'api_matching_mission_detail', methods: ['GET'])]
    public function getMissionDetail(int $id): JsonResponse
    {
        $mission = $this->matchingService->getMissionById($id);

        if (!$mission) {
            return $this->json([
                'success' => false,
                'error' => 'Mission introuvable',
            ], 404);
        }

        return $this->json([
            'success' => true,
            'mission' => $mission,
        ]);
    }
}