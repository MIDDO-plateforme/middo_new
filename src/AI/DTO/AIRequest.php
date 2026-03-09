<?php

namespace App\AI\DTO;

final class AIRequest
{
    public function __construct(
        private readonly string $model,
        private readonly string $prompt,
        private readonly array $options = [],
    ) {
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getPrompt(): string
    {
        return $this->prompt;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
