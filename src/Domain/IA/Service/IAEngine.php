<?php

namespace App\Domain\IA\Service;

use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;

class IAEngine
{
    public function __construct(
        private IAProviderInterface $provider
    ) {}

    public function generate(IARequest $request): IAResponse
    {
        return $this->provider->generate($request);
    }
}
