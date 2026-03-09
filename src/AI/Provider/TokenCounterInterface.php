<?php

namespace App\AI\Provider;

use App\AI\DTO\AIRequest;

interface TokenCounterInterface
{
    public function count(AIRequest $request): int;
}
