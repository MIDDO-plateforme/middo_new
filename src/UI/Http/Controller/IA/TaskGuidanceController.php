<?php

namespace App\UI\Http\Controller\IA;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Domain\IA\Pipeline\TaskGuidancePipeline;
use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IASettings;

class TaskGuidanceController
{
    public function __construct(
        private TaskGuidancePipeline $pipeline
    ) {}

    public function guide(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $payload = json_encode([
            'task' => $data['task'] ?? '',
            'image' => $data['image'] ?? null,
        ]);

        $iaRequest = new IARequest(
            prompt: $payload,
            settings: new IASettings()
        );

        $response = $this->pipeline->run($iaRequest);

        return new JsonResponse([
            'guidance' => $response->text,
        ]);
    }
}
