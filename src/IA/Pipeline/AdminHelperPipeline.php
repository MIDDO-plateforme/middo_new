<?php

namespace App\IA\Pipeline;

use App\IA\AiKernel;
use App\AI\DTO\AIResponse;

final class AdminHelperPipeline
{
    public function __construct(
        private readonly AiKernel $kernel
    ) {}

    /**
     * Analyse un texte administratif brut.
     */
    public function analyzeText(string $text, string $model = 'gpt-4o'): AIResponse
    {
        $prompt = <<<TXT
Tu es un assistant administratif universel.

Analyse ce texte administratif :
$text

Retourne :
- le type de document
- les informations importantes
- les obligations
- les délais
- les risques
- les actions à effectuer
TXT;

        return $this->kernel->askBest($prompt, $model, 'balanced');
    }

    /**
     * Simplifie un texte administratif pour un public non expert.
     */
    public function simplify(string $text, string $model = 'gpt-4o'): AIResponse
    {
        $prompt = <<<TXT
Simplifie ce texte administratif pour quelqu'un qui n'est pas expert :

$text

Règles :
- phrases courtes
- vocabulaire simple
- étapes claires
- pas de jargon
TXT;

        return $this->kernel->askBest($prompt, $model, 'quality');
    }

    /**
     * Génère un guide étape par étape.
     */
    public function generateGuide(string $text, string $model = 'gpt-4o'): AIResponse
    {
        $prompt = <<<TXT
À partir de ce texte administratif :

$text

Génère un guide étape par étape :
- ce qu'il faut préparer
- ce qu'il faut remplir
- où envoyer
- combien de temps ça prend
- erreurs à éviter
TXT;

        return $this->kernel->askBest($prompt, $model, 'balanced');
    }

    /**
     * Détecte automatiquement le type de document.
     */
    public function classify(string $text, string $model = 'gpt-4o'): AIResponse
    {
        $prompt = <<<TXT
Quel est le type de document suivant ?

$text

Réponds uniquement par :
- "formulaire"
- "attestation"
- "courrier"
- "notification"
- "décision"
- "facture"
- "contrat"
- "autre"
TXT;

        return $this->kernel->askRace($prompt, [$model, 'claude-3.5-sonnet']);
    }
}

