<?php

namespace App\Application\IA\Command;

class GenerateResponseCommand
{
    public string $userId;
    public string $prompt;
    public array $context;

    public function __construct(
        string $userId,
        string $prompt,
        array $context = []
    ) {
        $this->userId = $userId;
        $this->prompt = $prompt;
        $this->context = $context;
    }
}
