<?php

namespace App\Controller\IA;

use App\IA\AiKernel;
use App\IA\Security\IAInputSanitizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiIaController extends AbstractController
{
    #[Route('/api/ia', name: 'api_ia', methods: ['POST'])]
    public function __invoke(
        Request $request,
        AiKernel $aiKernel,
        IAInputSanitizer $sanitizer
    ): JsonResponse {
        $data = json_decode($request->getContent() ?: '{}', true);

        if (!isset($data['prompt']) || trim($data['prompt']) === '') {
            return $this->json(['error' => 'prompt manquant'], 400);
        }

        try {
            // Sécurisation du prompt
            $prompt = $sanitizer->sanitize($data['prompt']);

            // Appel IA (monitoring + fallback déjà dans AiKernel)
            $answer = $aiKernel->generate($prompt);

            return $this->json(['answer' => $answer]);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'IA indisponible'], 500);
        }
    }
}
