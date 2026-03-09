<?php

namespace App\IA\Monitor;

use App\AI\DTO\AIRequest;
use App\AI\DTO\AIResponse;

final class IAMonitor
{
    private array $entries = [];

    public function record(AIRequest $request, AIResponse $response, float $duration): void
    {
        $this->entries[] = [
            'timestamp' => (new \DateTimeImmutable())->format('c'),
            'model'     => $request->getModel(),
            'provider'  => $response->getProvider(),
            'tokens'    => $response->getTokens(),
            'duration'  => $duration,
        ];
    }

    public function all(): array
    {
        return $this->entries;
    }
}
