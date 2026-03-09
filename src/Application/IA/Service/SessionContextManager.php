<?php

namespace App\Application\IA\Service;

use App\Domain\IA\Service\SessionMemoryInterface;
use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;

class SessionContextManager
{
    public function __construct(
        private SessionMemoryInterface $memory
    ) {}

    public function loadContext(string $sessionId): array
    {
        return $this->memory->get($sessionId);
    }

    public function saveTurn(string $sessionId, IARequest $request, IAResponse $response): void
    {
        $context = $this->memory->get($sessionId);

        $context[] = [
            'prompt' => $request->prompt,
            'response' => $response->text,
        ];

        $this->memory->set($sessionId, $context);
    }
}
