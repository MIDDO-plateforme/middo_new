<?php

namespace App\Service\AI;

interface AIAssistantInterface
{
    public function ask(string $prompt, array $context = []): string;
}
