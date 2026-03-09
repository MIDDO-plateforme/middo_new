<?php

namespace App\AI\Provider;

use App\AI\DTO\AIRequest;
use App\AI\DTO\AIResponse;

/**
 * Interface premium unifiée pour tous les providers IA.
 * Compatible avec AiEngine, ProviderRouter et tous les pipelines.
 */
interface AIProviderInterface
{
    /**
     * Nom interne du provider (openai, anthropic, mistral, etc.).
     */
    public function getName(): string;

    /**
     * Indique si ce provider supporte le modèle demandé.
     * Exemple : gpt-4o → OpenAI, claude-3 → Anthropic, etc.
     */
    public function supportsModel(string $model): bool;

    /**
     * Génère une réponse IA normalisée.
     * Retourne toujours un AIResponse (Option 1 validée).
     */
    public function generate(AIRequest $request): AIResponse;

    /**
     * Test simple pour vérifier que le provider est opérationnel.
     */
    public function test(): string;
}
