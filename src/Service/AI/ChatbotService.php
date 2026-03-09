<?php

namespace App\Service\AI;

class ChatbotService
{
    public function __construct(
        private AIAssistantInterface $assistant
    ) {}

    public function reply(string $message, array $context = []): string
    {
        return $this->assistant->ask($message, $context);
    }
}
