<?php

namespace App\Infrastructure\IA\Provider;

use App\AI\DTO\AIRequest;
use App\AI\DTO\AIResponse;
use App\AI\Provider\AIProviderInterface;
use App\AI\Provider\TokenCounterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class AnthropicProvider implements AIProviderInterface
{
    private array $supportedModels = [
        'claude-3.5-sonnet',
        'claude-3.5-haiku',
        'claude-3-opus',
        'claude-3-sonnet',
        'claude-3-haiku',
    ];

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $apiKey,
        private readonly string $model,
        private readonly TokenCounterInterface $counter
    ) {}

    public function getName(): string
    {
        return 'anthropic';
    }

    public function supportsModel(string $model): bool
    {
        return in_array($model, $this->supportedModels, true)
            || str_starts_with($model, 'claude-');
    }

    public function generate(AIRequest $request): AIResponse
    {
        $prompt = $request->getPrompt();
        $tokens = $this->counter->count($request);

        return new AIResponse(
            $this->getName(),
            $this->model,
            sprintf('[Anthropic] Réponse simulée pour: "%s" (%d tokens)', $prompt, $tokens)
        );
    }

    public function test(): string
    {
        return '[Anthropic] Provider opérationnel (stub).';
    }
}
