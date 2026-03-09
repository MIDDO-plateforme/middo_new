<?php

namespace App\IA;

use App\AI\Provider\AIProviderInterface;

class AiRouter
{
    public function __construct(
        private readonly array $providers
    ) {}

    public function resolveProvider(string $model): AIProviderInterface
    {
        foreach ($this->providers as $provider) {
            if ($provider->supportsModel($model)) {
                return $provider;
            }
        }

        throw new \RuntimeException("Aucun provider ne supporte le modèle : $model");
    }

    public function getFallbackProviders(string $model, AIProviderInterface $failed): array
    {
        $fallbacks = [];

        foreach ($this->providers as $provider) {
            if ($provider !== $failed && $provider->supportsModel($model)) {
                $fallbacks[] = $provider;
            }
        }

        return $fallbacks;
    }

    public function resolveBestProvider(string $model, string $strategy = 'balanced'): AIProviderInterface
    {
        $scores = [
            'openai'      => ['q'=>10,'c'=>3,'s'=>7],
            'anthropic'   => ['q'=>10,'c'=>3,'s'=>7],
            'mistral'     => ['q'=>8,'c'=>2,'s'=>8],
            'deepseek'    => ['q'=>8,'c'=>1,'s'=>8],
            'perplexity'  => ['q'=>7,'c'=>2,'s'=>9],
            'gemini'      => ['q'=>9,'c'=>2,'s'=>9],
        ];

        $best = null;
        $bestScore = -9999;

        foreach ($this->providers as $name => $provider) {
            if (!$provider->supportsModel($model)) continue;

            $q = $scores[$name]['q'] ?? 5;
            $c = $scores[$name]['c'] ?? 3;
            $s = $scores[$name]['s'] ?? 5;

            $score = match ($strategy) {
                'quality'  => $q * 3 - $c,
                'cost'     => (10 - $c) * 3 + $s,
                'speed'    => $s * 3 + $q,
                default    => $q * 2 + $s - $c,
            };

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $provider;
            }
        }

        if (!$best) {
            throw new \RuntimeException("Aucun provider ne supporte le modèle : $model");
        }

        return $best;
    }

    public function raceResolve(array $models): array
    {
        foreach ($models as $model) {
            foreach ($this->providers as $provider) {
                if ($provider->supportsModel($model)) {
                    return [
                        'provider' => $provider,
                        'model' => $model,
                    ];
                }
            }
        }

        throw new \RuntimeException("Aucun provider ne supporte les modèles fournis.");
    }
}
