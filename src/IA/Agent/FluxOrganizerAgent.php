<?php

namespace App\IA\Agent;

use App\IA\AiKernel;

class FluxOrganizerAgent implements IaAgentInterface
{
    public function __construct(private AiKernel $kernel) {}

    public function getName(): string
    {
        return 'flux-organizer';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['flux-organize', 'organisation-flux', 'tri-flux']);
    }

    public function process(string $task, string $input): string
    {
        $prompt = <<<PROMPT
Tu es un agent d'organisation de flux.

Ta mission :
- Prendre un flux déjà ingéré et structuré (JSON).
- Le classer par catégories (administratif, business, personnel, technique, autre).
- Déterminer la priorité (basse, normale, haute, critique).
- Proposer une orientation : quel agent IA ou quel module doit traiter chaque élément.
- Produire un JSON structuré, prêt pour l'orchestration.

Format JSON attendu :
{
  "profil": "particulier|entrepreneur|institution|autre",
  "flux": [
    {
      "categorie": "administratif|business|personnel|technique|autre",
      "priorite": "basse|normale|haute|critique",
      "orientation": "admin|business|emotion|doc|autre",
      "contenu": "..."
    }
  ]
}

Flux structuré fourni :
$input
PROMPT;

        return $this->kernel->generate($prompt);
    }
}
