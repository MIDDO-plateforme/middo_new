<?php

namespace App\Service\AI;

interface AIAgentInterface
{
    public function handleIntent(string $intent, array $context = []): AIAgentResponse;
}
