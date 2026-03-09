<?php

namespace App\IA;

use App\AI\DTO\AIRequest;
use App\AI\DTO\AIResponse;
use App\AI\Provider\AIProviderInterface;

class AiEngine
{
    private array $providers = [];
    private ?AiRouter $router = null;

    public function registerProvider(AIProviderInterface $provider): void
    {
        $this->providers[$provider->getName()] = $provider;
        $this->router = new AiRouter($this->providers);
    }

    public function getProviders(): array
    {
        return $this->providers;
    }

    public function generate(string $model, AIRequest $request): AIResponse
    {
        $provider = $this->router->resolveProvider($model);
        return $provider->generate($request);
    }

    public function generateWithFallback(string $model, AIRequest $request): AIResponse
    {
        $primary = $this->router->resolveProvider($model);

        try {
            return $primary->generate($request);
        } catch (\Throwable $e) {
            foreach ($this->router->getFallbackProviders($model, $primary) as $fallback) {
                try {
                    return $fallback->generate($request);
                } catch (\Throwable) {}
            }

            throw new \RuntimeException("Tous les providers ont échoué pour $model", 0, $e);
        }
    }

    public function generateBest(string $model, AIRequest $request, string $strategy = 'balanced'): AIResponse
    {
        $provider = $this->router->resolveBestProvider($model, $strategy);
        return $provider->generate($request);
    }

    public function raceGenerate(array $models, AIRequest $request): AIResponse
    {
        $resolved = $this->router->raceResolve($models);
        return $resolved['provider']->generate($request);
    }
}
