<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/ia', name: 'api_ia_')]
class IaController extends AbstractController
{
    #[Route('/suggestions', name: 'suggestions', methods: ['POST'])]
    public function suggestions(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $text = $data['text'] ?? null;

        if (!$text) {
            return new JsonResponse(['error' => 'Missing text'], 400);
        }

        // Placeholder — OpenAI viendra ici
        return new JsonResponse([
            'result' => "Suggestions IA pour : " . $text
        ]);
    }

    #[Route('/summary', name: 'summary', methods: ['POST'])]
    public function summary(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $text = $data['text'] ?? null;

        if (!$text) {
            return new JsonResponse(['error' => 'Missing text'], 400);
        }

        return new JsonResponse([
            'result' => "Résumé IA pour : " . $text
        ]);
    }

    #[Route('/risks', name: 'risks', methods: ['POST'])]
    public function risks(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $text = $data['text'] ?? null;

        if (!$text) {
            return new JsonResponse(['error' => 'Missing text'], 400);
        }

        return new JsonResponse([
            'result' => "Analyse des risques IA pour : " . $text
        ]);
    }

    #[Route('/docgen', name: 'docgen', methods: ['POST'])]
    public function docgen(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $text = $data['text'] ?? null;

        if (!$text) {
            return new JsonResponse(['error' => 'Missing text'], 400);
        }

        return new JsonResponse([
            'result' => "Document généré par IA pour : " . $text
        ]);
    }
}
