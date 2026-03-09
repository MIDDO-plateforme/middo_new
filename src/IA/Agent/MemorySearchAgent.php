<?php

namespace App\IA\Agent;

use App\IA\AiKernel;
use App\IA\Memory\MemoryStore;

class MemorySearchAgent implements IaAgentInterface
{
    public function __construct(
        private AiKernel $kernel,
        private MemoryStore $store
    ) {}

    public function getName(): string
    {
        return 'memory-search';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['memoire-rechercher', 'memory-search', 'rappeler']);
    }

    public function process(string $task, string $input): string
    {
        $results = $this->store->search($input);
        $resultsJson = json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $prompt = <<<PROMPT
Tu es un agent de rappel de mémoire.

Tu reçois :
- une requête utilisateur
- une liste de souvenirs pertinents (JSON)

Ta mission :
- Synthétiser les éléments importants.
- Mettre en avant ce qui aide le plus la demande actuelle.
- Produire :
  - un résumé
  - une liste de points clés
  - éventuellement des liens entre les souvenirs.

Réponds en JSON :

{
  "resume": "...",
  "points_cles": ["...", "..."],
  "souvenirs_utilises": [...]
}

Requête :
$input

Souvenirs trouvés (JSON) :
$resultsJson
PROMPT;

        return $this->kernel->generate($prompt);
    }
}
