<?php

namespace App\Service\AI;

class SuggestionService
{
    public function __construct(
        private AIAssistantInterface $assistant
    ) {}

    public function suggestForProject(string $description): string
    {
        $prompt = 'Propose des améliorations pour ce projet : ' . $description;

        return $this->assistant->ask($prompt);
    }
}
