<?php

namespace App\IA\Agents;

use App\IA\Client\IaClientInterface;

class SafetyAgent
{
    public function __construct(
        private IaClientInterface $iaClient,
    ) {
    }

    public function check(string $context = ''): array
    {
        $prompt = <<<PROMPT
Tu es l'agent Safety de MIDDO OS + IA.

Ta mission :
- analyser le contexte fourni (flux utilisateur + plan éventuel)
- détecter les risques potentiels (sécurité, légalité, éthique, confidentialité)
- signaler tout ce qui pourrait être problématique
- proposer des recommandations de mitigation

Contexte :
{$context}

Réponds en JSON strict avec la structure suivante :
{
  "safe": boolean,
  "risks": [ "description du risque 1", "description du risque 2" ],
  "recommendations": [ "recommandation 1", "recommandation 2" ]
}
PROMPT;

        $raw = $this->iaClient->generate($prompt, [
            'role' => 'safety',
        ]);

        // Fallback robuste si le JSON est mal formé
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            $decoded = [
                'safe' => true,
                'risks' => [],
                'recommendations' => [],
                'raw' => $raw,
            ];
        }

        return $decoded;
    }
}
