<?php

namespace App\Domain\IA\Pipeline\Step;

use App\Domain\IA\Pipeline\IAPipelineStepInterface;
use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;

class SafetyStep implements IAPipelineStepInterface
{
    public function process(IARequest $request, ?IAResponse $previousResponse = null): IAResponse
    {
        $prompt = strtolower($request->prompt);

        $blocked = [
            'violence',
            'kill',
            'suicide',
            'bomb',
            'terrorism',
            'nsfw',
            'porn',
        ];

        foreach ($blocked as $word) {
            if (str_contains($prompt, $word)) {
                return new IAResponse(
                    text: "Votre demande ne peut pas être traitée pour des raisons de sécurité.",
                    tokensUsed: 0
                );
            }
        }

        return $previousResponse ?? new IAResponse('', 0);
    }
}
