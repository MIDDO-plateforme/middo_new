<?php

namespace App\Infrastructure\IA\Service;

use App\AI\Provider\TokenCounterInterface;
use App\AI\DTO\AIRequest;

/**
 * Compteur de tokens premium pour MIDDO OS + IA.
 */
final class TokenCounter implements TokenCounterInterface
{
    public function count(AIRequest $request): int
    {
        return strlen($request->getPrompt());
    }
}
