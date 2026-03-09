<?php

namespace App\Service\AI;

class AIAgentResponse
{
    public function __construct(
        public string $type,
        public string $message,
        public array $data = []
    ) {}
}
