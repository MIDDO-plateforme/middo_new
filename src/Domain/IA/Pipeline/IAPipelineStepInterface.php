<?php

namespace App\Domain\IA\Pipeline;

use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;

interface IAPipelineStepInterface
{
    public function process(IARequest $request, ?IAResponse $previousResponse = null): IAResponse;
}
