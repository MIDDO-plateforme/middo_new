<?php

namespace App\Infrastructure\IA\Provider;

use App\AI\DTO\AIRequest;
use App\AI\DTO\AIResponse;
use App\AI\Provider\AIProviderInterface;
use App\AI\Provider\TokenCounterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class GeminiProvider implements AIProviderInterface
{
    private array $supportedModels = [
        'gemini-1.5-pro',
        'gemini-1.5-flash',
        'gemini-1.5-flash-8b',
        'gemini-1.0-pro',
        'gemini-1.0-ultra',
    ];

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $apiKey,
        private readonly string $model,
        private readonly TokenCounterInterface $counter
    ) {}

    public function getName(): string
    {
        return 'gemini';
    }

    public function supportsModel(string $model): bool
    {
        return in_array($model, $this->supportedModels, true)
            || str_contains($model, 'gemini');
    }

    public function generate(AIRequest $request): AIResponse
    {
        $prompt = $request->getPrompt();
        $tokens = $this->counter->count($request);

        return new AIResponse(
            $this->getName(),
            $this->model,
            sprintf('[Gemini] Réponse simulée pour: "%s" (%d tokens)', $prompt, $tokens)
        );
    }

    public function test(): string
    {
        return '[Gemini] Provider opérationnel (stub).';
    }
}
