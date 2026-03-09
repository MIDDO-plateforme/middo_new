<?php

namespace App\Service\AI;

class CortexEngine
{
    public function __construct(
        private OpenAIAssistantService $externalAI,
        private AiEngineService $internalAI
    ) {}

    public function process(string $intent, array $context = []): AIAgentResponse
    {
        // Règle simple : si l’intention commence par "system:" → IA interne
        if (str_starts_with($intent, 'system:')) {
            return $this->internalAI->handleIntent($intent, $context);
        }

        // Sinon → IA externe
        return $this->externalAI->handleIntent($intent, $context);
    }
}
