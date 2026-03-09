<?php

namespace App\Infrastructure\IA\Provider;

use App\AI\DTO\AIRequest;
use App\AI\DTO\AIResponse;
use App\AI\Provider\AIProviderInterface;
use App\AI\Provider\TokenCounterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class PerplexityProvider implements AIProviderInterface
{
    private array $supportedModels = [
        'pplx-70b-online',
        'pplx-70b-chat',
        'pplx-7b-online',
        'pplx-7b-chat',
        'sonar-small-chat',
        'sonar-medium-chat',
        'sonar-large-chat',
    ];

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $apiKey,
        private readonly string $model,
        private readonly TokenCounterInterface $counter
    ) {}

    public function getName(): string
    {
        return 'perplexity';
    }

    public function supportsModel(string $model): bool
    {
        return in_array($model, $this->supportedModels, true)
            || str_contains($model, 'pplx')
            || str_contains($model, 'sonar');
    }

    public function generate(AIRequest $request): AIResponse
    {
        $prompt = $request->getPrompt();
        $tokens = $this->counter->count($request);

        return new AIResponse(
            $this->getName(),
            $this->model,
            sprintf('[Perplexity] Réponse simulée pour: "%s" (%d tokens)', $prompt, $tokens)
        );
    }

    public function test(): string
    {
        return '[Perplexity] Provider opérationnel (stub).';
    }
}
