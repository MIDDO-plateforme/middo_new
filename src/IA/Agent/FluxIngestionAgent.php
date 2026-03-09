<?php

namespace App\IA\Agent;

use App\IA\AiKernel;

class FluxIngestionAgent implements IaAgentInterface
{
    public function __construct(private AiKernel $kernel) {}

    public function getName(): string
    {
        return 'flux-ingestion';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['flux-ingest', 'ingestion', 'collecte-flux']);
    }

    public function process(string $task, string $input): string
    {
        $prompt = <<<PROMPT
Tu es un agent d'ingestion de flux.

Ta mission :
- Recevoir des flux hétérogènes (texte brut, emails, documents, notes, logs, etc.).
- Extraire les éléments importants.
- Normaliser le contenu.
- Produire une représentation structurée en JSON, prête à être classée et orientée.

Format JSON attendu :
{
  "brut": "...",
  "extraits": [
    {"type": "info", "contenu": "..."},
    {"type": "action", "contenu": "..."}
  ],
  "meta": {
    "source": "inconnue|email|document|autre",
    "priorite": "basse|normale|haute",
    "sensibilite": "faible|moyenne|elevee"
  }
}

Flux reçu :
$input
PROMPT;

        return $this->kernel->generate($prompt);
    }
}
