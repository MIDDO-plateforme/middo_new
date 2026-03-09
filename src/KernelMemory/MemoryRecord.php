<?php

namespace App\KernelMemory;

class MemoryRecord
{
    public function __construct(
        public int $timestamp,
        public array $state
    ) {}

    public function toArray(): array
    {
        return [
            'timestamp' => $this->timestamp,
            'state' => $this->state,
        ];
    }
}
