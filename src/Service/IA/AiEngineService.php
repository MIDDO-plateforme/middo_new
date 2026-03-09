<?php

namespace App\Service\IA;

use App\IA\AiEngine;
use App\Infrastructure\IA\Provider\OpenAIProvider;
use App\Infrastructure\IA\Provider\AnthropicProvider;
use App\AI\Provider\TokenCounterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class AiEngineService
{
    private AiEngine $engine;

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly TokenCounterInterface $counter,
        private readonly string $openaiApiKey,
        private readonly string $openaiModel,
        private readonly string $anthropicApiKey,
        private readonly string $anthropicModel,
    ) {
        $this->engine = new AiEngine();

        // Enregistrement des providers premium
        $this->engine->registerProvider(
            new OpenAIProvider(
                $this->client,
                $this->openaiApiKey,
                $this->openaiModel,
                $this->counter
            )
        );

        $this->engine->registerProvider(
            new AnthropicProvider(
                $this->client,
                $this->anthropicApiKey,
                $this->anthropicModel,
                $this->counter
            )
        );
    }

    public function getEngine(): AiEngine
    {
        return $this->engine;
    }
}
