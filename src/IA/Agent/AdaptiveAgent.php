<?php

namespace App\IA\Agent;

use App\IA\AiKernel;

class AdaptiveAgent implements IaAgentInterface
{
    public function __construct(private AiKernel $kernel) {}

    public function getName(): string
    {
        return 'adaptive';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['adaptatif', 'adaptive', 'auto-adapt']);
    }

    public function process(string $task, string $input): string
    {
        $prompt = <<<PROMPT
Tu es un agent auto-adaptatif.

Ta mission :
- Adapter le ton, le niveau de détail, la structure et les priorités
  en fonction :
  - du profil (particulier, entrepreneur, institution, autre)
  - de la charge de flux
  - du contexte (urgence, complexité, sensibilité)
- Proposer une réponse optimisée pour réduire la contrainte cognitive.
- Produire :
  - une réponse lisible
  - un bloc JSON "meta_adaptation" expliquant les choix d'adaptation.

Contexte et demande :
$input
PROMPT;

        return $this->kernel->generate($prompt);
    }
}
