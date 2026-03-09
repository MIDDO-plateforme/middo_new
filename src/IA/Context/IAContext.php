<?php

namespace App\IA\Context;

final class IAContext
{
    public function __construct(
        public readonly string $language = 'fr',
        public readonly string $country = 'FR',
        public readonly string $expertise = 'beginner', // beginner | intermediate | expert
        public readonly string $goal = 'general',        // analyse | simplify | translate | guide | classify | etc.
        public readonly string $tone = 'neutral',        // neutral | friendly | formal | pedagogical
        public readonly array $constraints = [],         // ex: ['max_length' => 200]
        public readonly array $metadata = []             // libre
    ) {}

    public function with(array $changes): self
    {
        return new self(
            $changes['language']    ?? $this->language,
            $changes['country']     ?? $this->country,
            $changes['expertise']   ?? $this->expertise,
            $changes['goal']        ?? $this->goal,
            $changes['tone']        ?? $this->tone,
            $changes['constraints'] ?? $this->constraints,
            $changes['metadata']    ?? $this->metadata,
        );
    }
}
