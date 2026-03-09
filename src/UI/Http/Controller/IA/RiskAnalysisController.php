<?php

namespace App\UI\Http\Controller\IA;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\IA\Handler\RiskAnalysisHandler;
use App\Application\IA\Command\GenericIACommand;

class RiskAnalysisController
{
    public function __construct(
        private RiskAnalysisHandler $handler
    ) {}

    public function analyze(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $command = new GenericIACommand(
            prompt: $data['text'] ?? ''
        );

        return new JsonResponse($this->handler->handle($command));
    }
}
