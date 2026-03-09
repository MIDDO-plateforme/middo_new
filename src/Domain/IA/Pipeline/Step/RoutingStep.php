<?php

namespace App\Domain\IA\Pipeline\Step;

use App\Domain\IA\Pipeline\IAPipelineStepInterface;
use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;

/**
 * RoutingStep : choisit dynamiquement le provider IA à utiliser.
 */
class RoutingStep implements IAPipelineStepInterface
{
    public function __construct(
        private array $providers // ['openai' => OpenAIProvider, 'anthropic' => AnthropicProvider, ...]
    ) {}

    public function process(IARequest $request, ?IAResponse $previousResponse = null): IAResponse
    {
        $prompt = strtolower($request->prompt);

        // Exemple simple : tu pourras enrichir plus tard
        if (str_contains($prompt, 'analyse') || str_contains($prompt, 'résume')) {
            $chosen = 'anthropic';
        } elseif (str_contains($prompt, 'code') || str_contains($prompt, 'programmation')) {
            $chosen = 'openai';
        } else {
            $chosen = 'openai';
        }

        // On stocke le provider choisi dans une IAResponse intermédiaire
        return new IAResponse(
            text: $chosen,
            tokensUsed: 0
        );
    }
}
