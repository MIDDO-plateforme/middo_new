<?php

namespace App\IA\Client;

class IaRouter implements IaClientInterface
{
    public function __construct(
        private IaClientInterface $openAiClient,
        private IaClientInterface $anthropicClient,
        private IaClientInterface $middoCoreClient,
    ) {
    }

    public function generate(string $prompt, array $options = []): string
    {
        $target = $options['target'] ?? 'openai';

        return match ($target) {
            'openai' => $this->openAiClient->generate($prompt, $options),
            'anthropic' => $this->anthropicClient->generate($prompt, $options),
            'middo' => $this->middoCoreClient->generate($prompt, $options),
            default => $this->openAiClient->generate($prompt, $options),
        };
    }
}
