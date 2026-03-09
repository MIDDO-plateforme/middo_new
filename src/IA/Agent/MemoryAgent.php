<?php

namespace App\IA\Agent;

use App\IA\AiKernel;
use App\IA\Memory\MemoryStore;

class MemoryAgent implements IaAgentInterface
{
    public function __construct(
        private AiKernel $kernel,
        private MemoryStore $store
    ) {}

    public function getName(): string
    {
        return 'memory-write';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['memoire-ecrire', 'memory-write', 'memoriser']);
    }

    public function process(string $task, string $input): string
    {
        $prompt = <<<PROMPT
Tu es un agent de mémorisation.

Ta mission :
- Recevoir une information brute (texte, contexte, décision, flux, etc.).
- En extraire :
  - un résumé court
  - des mots-clés
  - un type (decision, info, preference, contexte, autre)
  - un niveau d'importance (basse, normale, haute, critique)
- Produire un JSON structuré :

{
  "resume": "...",
  "type": "...",
  "importance": "...",
  "mots_cles": ["...", "..."],
  "contenu_original": "..."
}

Information à mémoriser :
$input
PROMPT;

        $json = $this->kernel->generate($prompt);

        $decoded = json_decode($json, true);
        if (is_array($decoded)) {
            $this->store->add($decoded);
        }

        return $json;
    }
}
