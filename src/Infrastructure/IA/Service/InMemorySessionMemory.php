<?php

namespace App\Infrastructure\IA\Service;

use App\Domain\IA\Service\SessionMemoryInterface;

class InMemorySessionMemory implements SessionMemoryInterface
{
    /** @var array<string, array> */
    private array $store = [];

    public function get(string $sessionId): array
    {
        return $this->store[$sessionId] ?? [];
    }

    public function set(string $sessionId, array $data): void
    {
        $this->store[$sessionId] = $data;
    }

    public function clear(string $sessionId): void
    {
        unset($this->store[$sessionId]);
    }
}
