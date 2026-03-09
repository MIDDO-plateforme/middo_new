<?php

namespace App\Domain\IA\Service;

interface SessionMemoryInterface
{
    public function get(string $sessionId): array;

    public function set(string $sessionId, array $data): void;

    public function clear(string $sessionId): void;
}
