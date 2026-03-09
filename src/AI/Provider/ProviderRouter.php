<?php

namespace App\AI\Provider;

use App\AI\DTO\AIRequest;
use App\AI\DTO\AIResponse;
use App\AI\Exception\ProviderException;

final class ProviderRouter
{
    private array $providers;

    public function __construct(iterable $providers)
    {
        $this->providers = [];
        foreach ($providers as $provider) {
            $this->providers[$provider->getName()] = $provider;
        }
    }

    public function route(AIRequest $request): AIResponse
    {
        $providerName = $request->getProviderName();
        $model = $request->getModel();

        if (!isset($this->providers[$providerName])) {
            throw new ProviderException(sprintf('Unknown provider "%s".', $providerName));
        }

        $provider = $this->providers[$providerName];

        if (!$provider->supportsModel($model)) {
            throw new ProviderException(sprintf(
                'Provider "%s" does not support model "%s".',
                $providerName,
                $model
            ));
        }

        return $provider->generate($request);
    }
}
