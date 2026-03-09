<?php

namespace App\IA\Registry;

use App\AI\DTO\AIRequest;
use App\AI\DTO\AIResponse;

final class IARegistry
{
    private array $entries = [];

    public function record(AIRequest $request, AIResponse $response, array $extra = []): void
    {
        $this->entries[] = [
            'timestamp' => (new \DateTimeImmutable())->format('c'),
            'model'     => $request->getModel(),
            'prompt'    => $request->getPrompt(),
            'provider'  => $response->getProvider(),
            'response'  => $response->getContent(),
            'tokens'    => $response->getTokens(),
            'extra'     => $extra,
        ];
    }

    public function all(): array
    {
        return $this->entries;
    }

    public function last(): ?array
    {
        return $this->entries[count($this->entries) - 1] ?? null;
    }
}
