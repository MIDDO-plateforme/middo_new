<?php

namespace App\Controller\IA;

use App\IA\Pipeline\ProjectPipeline;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/ia/project')]
final class ProjectIAController extends AbstractController
{
    public function __construct(
        private readonly ProjectPipeline $pipeline
    ) {}

    #[Route('/plan', name: 'api_ia_project_plan', methods: ['POST'])]
    public function plan(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['goal'])) {
            return $this->json(['error' => 'Missing field: goal'], 400);
        }

        $response = $this->pipeline->plan($data['goal']);

        return $this->json([
            'content' => $response->getContent(),
            'provider' => $response->getProvider(),
            'model' => $response->getModel(),
            'tokens' => $response->getTokens(),
        ]);
    }
}
