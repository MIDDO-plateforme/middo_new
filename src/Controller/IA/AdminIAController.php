<?php

namespace App\Controller\IA;

use App\IA\Pipeline\AdminHelperPipeline;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/ia/admin')]
final class AdminIAController extends AbstractController
{
    public function __construct(
        private readonly AdminHelperPipeline $pipeline
    ) {}

    #[Route('/analyze', name: 'api_ia_admin_analyze', methods: ['POST'])]
    public function analyze(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['text'])) {
            return $this->json(['error' => 'Missing field: text'], 400);
        }

        $response = $this->pipeline->analyzeText($data['text']);

        return $this->json([
            'content' => $response->getContent(),
            'provider' => $response->getProvider(),
            'model' => $response->getModel(),
            'tokens' => $response->getTokens(),
        ]);
    }

    #[Route('/simplify', name: 'api_ia_admin_simplify', methods: ['POST'])]
    public function simplify(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['text'])) {
            return $this->json(['error' => 'Missing field: text'], 400);
        }

        $response = $this->pipeline->simplify($data['text']);

        return $this->json([
            'content' => $response->getContent(),
            'provider' => $response->getProvider(),
            'model' => $response->getModel(),
            'tokens' => $response->getTokens(),
        ]);
    }

    #[Route('/guide', name: 'api_ia_admin_guide', methods: ['POST'])]
    public function guide(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['text'])) {
            return $this->json(['error' => 'Missing field: text'], 400);
        }

        $response = $this->pipeline->generateGuide($data['text']);

        return $this->json([
            'content' => $response->getContent(),
            'provider' => $response->getProvider(),
            'model' => $response->getModel(),
            'tokens' => $response->getTokens(),
        ]);
    }

    #[Route('/classify', name: 'api_ia_admin_classify', methods: ['POST'])]
    public function classify(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['text'])) {
            return $this->json(['error' => 'Missing field: text'], 400);
        }

        $response = $this->pipeline->classify($data['text']);

        return $this->json([
            'content' => $response->getContent(),
            'provider' => $response->getProvider(),
            'model' => $response->getModel(),
            'tokens' => $response->getTokens(),
        ]);
    }
}
