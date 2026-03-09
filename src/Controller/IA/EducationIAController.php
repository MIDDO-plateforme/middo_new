<?php

namespace App\Controller\IA;

use App\IA\Pipeline\EducationPipeline;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/ia/educate')]
final class EducationIAController extends AbstractController
{
    public function __construct(
        private readonly EducationPipeline $pipeline
    ) {}

    #[Route('/explain', name: 'api_ia_educate_explain', methods: ['POST'])]
    public function explain(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['topic'])) {
            return $this->json(['error' => 'Missing field: topic'], 400);
        }

        $level = $data['level'] ?? 'beginner';

        $response = $this->pipeline->explain($data['topic'], $level);

        return $this->json([
            'content' => $response->getContent(),
            'provider' => $response->getProvider(),
            'model' => $response->getModel(),
            'tokens' => $response->getTokens(),
        ]);
    }
}
