<?php

namespace App\IA\Agent;

use App\IA\AiKernel;

class CountryAwareAgent implements IaAgentInterface
{
    public function __construct(
        private AiKernel $kernel,
        private string $country = 'FR'
    ) {}

    public function getName(): string
    {
        return 'country-aware';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['pays', 'country', 'context-pays']);
    }

    public function process(string $task, string $input): string
    {
        $prompt = <<<PROMPT
Tu es un agent spécialisé par pays.

Pays actuel : {$this->country}

Ta mission :
- Adapter les informations à ce pays
- Prendre en compte les lois, démarches, culture, administration
- Répondre de manière contextualisée

Demande :
$input
PROMPT;

        return $this->kernel->generate($prompt);
    }
}
