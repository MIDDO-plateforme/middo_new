<?php

namespace App\IA\Health;

use App\IA\AiKernel;

final class IAHealthCheck
{
    public function __construct(
        private readonly AiKernel $kernel
    ) {}

    public function check(): array
    {
        $results = [];

        try {
            $response = $this->kernel->ask("ping", "gpt-4o");
            $results['openai'] = $response->getContent() !== null;
        } catch (\Throwable) {
            $results['openai'] = false;
        }

        try {
            $response = $this->kernel->ask("ping", "claude-3.5-sonnet");
            $results['anthropic'] = $response->getContent() !== null;
        } catch (\Throwable) {
            $results['anthropic'] = false;
        }

        return $results;
    }
}
