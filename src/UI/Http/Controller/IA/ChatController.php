<?php

namespace App\UI\Http\Controller\IA;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\IA\Orchestrator\IAOrchestrator;
use App\Application\IA\Command\GenericIACommand;

class ChatController
{
    public function __construct(
        private IAOrchestrator $orchestrator
    ) {}

    public function chat(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $command = new GenericIACommand(
            prompt: $data['text'] ?? '',
            sessionId: $data['sessionId'] ?? null,
            targetLanguage: $data['targetLanguage'] ?? null
        );

        $response = $this->orchestrator->handle($command);

        return new JsonResponse([
            'response' => $response->text,
            'tokens' => $response->tokensUsed,
        ]);
    }
}
