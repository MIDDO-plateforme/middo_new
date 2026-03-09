<?php

namespace App\Domain\IA\Pipeline\Step;

use App\Domain\IA\Pipeline\IAPipelineStepInterface;
use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;

class PostProcessStep implements IAPipelineStepInterface
{
    public function process(IARequest $request, ?IAResponse $previousResponse = null): IAResponse
    {
        if (!$previousResponse) {
            return new IAResponse('', 0);
        }

        $clean = trim($previousResponse->text);

        return new IAResponse(
            text: $clean,
            tokensUsed: $previousResponse->tokensUsed
        );
    }
}
