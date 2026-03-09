<?php

namespace App\AI\DTO;

final class AIResponse
{
    public function __construct(
        private readonly string $providerName,
        private readonly string $model,
        private readonly string $content,
        private readonly array $meta = [],
    ) {
    }

    public function getProviderName(): string
    {
        return $this->providerName;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }
}
