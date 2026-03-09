<?php

namespace App\IA\Agent;

use App\IA\AiKernel;
use App\IA\Memory\MemoryStore;

class MemorySupervisorAgent implements IaAgentInterface
{
    public function __construct(
        private AiKernel $kernel,
        private MemoryStore $store
    ) {}

    public function getName(): string
    {
        return 'memory-supervisor';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['memoire-supervision', 'memory-supervisor', 'hygiene-memoire']);
    }

    public function process(string $task, string $input): string
    {
        $all = $this->store->getAll();
        $count = $this->store->count();
        $allJson = json_encode($all, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $prompt = <<<PROMPT
Tu es un agent de supervision de la mémoire longue durée.

Tu reçois :
- l'ensemble des souvenirs stockés (JSON)
- le nombre total d'entrées
- une éventuelle consigne utilisateur

Ta mission :
- Evaluer l'état de la mémoire :
  - volume
  - redondance potentielle
  - déséquilibres (trop de tel type, pas assez d'un autre)
- Proposer :
  - des recommandations de nettoyage ou de compression
  - des priorités de conservation
  - des axes d'amélioration.

Réponds en JSON :

{
  "etat": "leger|normal|lourd|critique",
  "volume": <nombre>,
  "alertes": ["...", "..."],
  "recommandations": ["...", "..."]
}

Nombre total d'entrées :
$count

Souvenirs (JSON) :
$allJson

Consigne utilisateur :
$input
PROMPT;

        return $this->kernel->generate($prompt);
    }
}
