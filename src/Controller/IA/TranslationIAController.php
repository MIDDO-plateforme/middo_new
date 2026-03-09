<?php

namespace App\Controller\IA;

use App\IA\Pipeline\TranslationPipeline;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/ia/translate')]
final class TranslationIAController extends AbstractController
{
    public function __construct(
        private readonly TranslationPipeline $pipeline
    ) {}

    #[Route('/text', name: 'api_ia_translate_text', methods: ['POST'])]
    public function translate(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['text'])) {
            return $this->json(['error' => 'Missing field: text'], 400);
        }

        $target = $data['target'] ?? 'fr';

        $response = $this->pipeline->translate($data['text'], $target);

        return $this->json([
            'content' => $response->getContent(),
            'provider' => $response->getProvider(),
            'model' => $response->getModel(),
            'tokens' => $response->getTokens(),
        ]);
    }
}
