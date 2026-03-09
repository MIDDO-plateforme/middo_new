<?php

namespace App\UI\Http\Controller\IA;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\IA\Handler\VoiceTranslationHandler;
use App\Application\IA\Command\GenericIACommand;

class VoiceTranslationController
{
    public function __construct(
        private VoiceTranslationHandler $handler
    ) {}

    public function translate(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $command = new GenericIACommand(
            prompt: $data['audio'] ?? '',
            targetLanguage: $data['targetLanguage'] ?? 'fr'
        );

        return new JsonResponse($this->handler->handle($command));
    }
}
