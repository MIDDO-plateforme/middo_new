<?php

namespace App\Domain\IA\ValueObject;

use App\Domain\User\ValueObject\IaSettings;

class IARequest
{
    public string $prompt;
    public IaSettings $settings;
    public array $context;

    public function __construct(
        string $prompt,
        IaSettings $settings,
        array $context = []
    ) {
        $this->prompt = $prompt;
        $this->settings = $settings;
        $this->context = $context;
    }
}
