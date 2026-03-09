<?php

namespace App\Domain\User\ValueObject;

class IaSettings
{
    public string $tone;
    public float $temperature;
    public int $maxTokens;

    public function __construct(
        string $tone = 'neutral',
        float $temperature = 0.7,
        int $maxTokens = 512
    ) {
        $this->tone = $tone;
        $this->temperature = $temperature;
        $this->maxTokens = $maxTokens;
    }
}
