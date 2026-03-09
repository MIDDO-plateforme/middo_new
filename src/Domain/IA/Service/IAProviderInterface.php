<?php

namespace App\Domain\IA\Service;

use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;

interface IAProviderInterface
{
    public function generate(IARequest $request): IAResponse;
}
