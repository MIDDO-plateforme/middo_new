<?php

namespace App\Infrastructure\IA\Provider;

use App\AI\DTO\AIRequest;
use App\AI\DTO\AIResponse;
use App\AI\Provider\AIProviderInterface;
use App\AI\Provider\TokenCounterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class DeepSeekProvider implements AIProviderInterface
{
    private array $supportedModels = [
        'deepseek-chat',
        'deepseek-coder',
        'deepseek-r1',
        'deepseek-v3',
    ];

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $apiKey,
        private readonly string $model,
        private readonly TokenCounterInterface $counter
    ) {}

    public function getName(): string
    {
        return 'deepseek';
    }

    public function supportsModel(string $model): bool
    {
        return in_array($model, $this->supportedModels, true)
            || str_contains($model, 'deepseek');
    }

    public function generate(AIRequest $request): AIResponse
    {
        $prompt = $request->getPrompt();
        $tokens = $this->counter->count($request);

        return new AIResponse(
            $this->getName(),
            $this->model,
            sprintf('[DeepSeek] Réponse simulée pour: "%s" (%d tokens)', $prompt, $tokens)
        );
    }

    public function test(): string
    {
        return '[DeepSeek] Provider opérationnel (stub).';
    }
}
