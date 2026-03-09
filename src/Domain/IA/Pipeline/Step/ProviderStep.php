<?php

namespace App\Domain\IA\Pipeline\Step;

use App\Domain\IA\Pipeline\IAPipelineStepInterface;
use App\Domain\IA\Service\IAProviderInterface;
use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;

class ProviderStep implements IAPipelineStepInterface
{
    public function __construct(
        private array $providers // ['openai' => OpenAIProvider, 'anthropic' => AnthropicProvider]
    ) {}

    public function process(IARequest $request, ?IAResponse $previousResponse = null): IAResponse
    {
        if (!$previousResponse) {
            throw new \RuntimeException('ProviderStep: aucun provider choisi.');
        }

        $providerName = trim($previousResponse->text);

        if (!isset($this->providers[$providerName])) {
            throw new \RuntimeException("ProviderStep: provider '$providerName' introuvable.");
        }

        /** @var IAProviderInterface $provider */
        $provider = $this->providers[$providerName];

        return $provider->generate($request);
    }
}
