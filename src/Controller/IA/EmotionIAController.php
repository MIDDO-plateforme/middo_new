<?php

namespace App\Controller\IA;

use App\IA\Pipeline\EmotionPipeline;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/ia/emotion')]
final class EmotionIAController extends AbstractController
{
    public function __construct(
        private readonly EmotionPipeline $pipeline
    ) {}

    #[Route('/detect', name: 'api_ia_emotion_detect', methods: ['POST'])]
    public function detect(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['text'])) {
            return $this->json(['error' => 'Missing field: text'], 400);
        }

        $response = $this->pipeline->detectEmotion($data['text']);

        return $this->json([
            'content' => $response->getContent(),
            'provider' => $response->getProvider(),
            'model' => $response->getModel(),
            'tokens' => $response->getTokens(),
        ]);
    }
}
