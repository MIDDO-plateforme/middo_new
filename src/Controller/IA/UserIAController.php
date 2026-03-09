<?php

namespace App\Controller\IA;

use App\IA\Pipeline\UserPipeline;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/ia/user')]
final class UserIAController extends AbstractController
{
    public function __construct(
        private readonly UserPipeline $pipeline
    ) {}

    #[Route('/summary', name: 'api_ia_user_summary', methods: ['POST'])]
    public function summary(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['user'])) {
            return $this->json(['error' => 'Missing field: user'], 400);
        }

        $response = $this->pipeline->summarizeProfile($data['user']);

        return $this->json([
            'content' => $response->getContent(),
            'provider' => $response->getProvider(),
            'model' => $response->getModel(),
            'tokens' => $response->getTokens(),
        ]);
    }
}
