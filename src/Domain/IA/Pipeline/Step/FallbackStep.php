<?php

namespace App\Domain\IA\Pipeline\Step;

use App\Domain\IA\Pipeline\IAPipelineStepInterface;
use App\Domain\IA\Service\IAProviderInterface;
use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;

class FallbackStep implements IAPipelineStepInterface
{
    public function __construct(
        private array $fallbackProviders // ['anthropic' => AnthropicProvider]
    ) {}

    public function process(IARequest $request, ?IAResponse $previousResponse = null): IAResponse
    {
        if ($previousResponse instanceof IAResponse && $previousResponse->text !== '__ERROR__') {
            return $previousResponse;
        }

        foreach ($this->fallbackProviders as $provider) {
            try {
                return $provider->generate($request);
            } catch (\Throwable $e) {
                continue;
            }
        }

        return new IAResponse(
            text: "Tous les providers IA ont échoué.",
            tokensUsed: 0
        );
    }
}
