<?php

namespace App\AI\Provider;

final class ProviderCapabilities
{
    public function __construct(
        private readonly string $name,
        private readonly array $models,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getModels(): array
    {
        return $this->models;
    }

    public function supports(string $model): bool
    {
        return in_array($model, $this->models, true);
    }
}
