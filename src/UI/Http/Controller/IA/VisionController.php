<?php

namespace App\UI\Http\Controller\IA;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Domain\IA\Pipeline\VisionPipeline;
use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IASettings;

class VisionController
{
    public function __construct(
        private VisionPipeline $pipeline
    ) {}

    public function analyze(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $iaRequest = new IARequest(
            prompt: $data['image'] ?? '',
            settings: new IASettings(
                targetLanguage: $data['targetLanguage'] ?? 'fr'
            )
        );

        $response = $this->pipeline->run($iaRequest);

        return new JsonResponse([
            'description' => $response->text,
        ]);
    }
}
