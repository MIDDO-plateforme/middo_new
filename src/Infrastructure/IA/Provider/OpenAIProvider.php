<?php

namespace App\Infrastructure\IA\Provider;

use App\AI\DTO\AIRequest;
use App\AI\DTO\AIResponse;
use App\AI\Provider\AIProviderInterface;
use App\AI\Provider\TokenCounterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class OpenAIProvider implements AIProviderInterface
{
    private array $supportedModels = [
        'gpt-4.1',
        'gpt-4.1-mini',
        'gpt-4o',
        'gpt-4o-mini',
        'gpt-4-turbo',
        'gpt-4',
        'gpt-3.5-turbo',
    ];

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $apiKey,
        private readonly string $model,
        private readonly TokenCounterInterface $counter
    ) {}

    public function getName(): string
    {
        return 'openai';
    }

    public function supportsModel(string $model): bool
    {
        return in_array($model, $this->supportedModels, true)
            || str_starts_with($model, 'gpt-');
    }

    public function generate(AIRequest $request): AIResponse
    {
        $prompt = $request->getPrompt();
        $tokens = $this->counter->count($request);

        return new AIResponse(
            $this->getName(),
            $this->model,
            sprintf('[OpenAI] Réponse simulée pour: "%s" (%d tokens)', $prompt, $tokens)
        );
    }

    public function test(): string
    {
        return '[OpenAI] Provider opérationnel (stub).';
    }
}
