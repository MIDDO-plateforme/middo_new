<?php

namespace App\Domain\IA\Pipeline\Step;

use App\Domain\IA\Pipeline\IAPipelineStepInterface;
use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;
use Psr\Log\LoggerInterface;

class LoggingStep implements IAPipelineStepInterface
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    public function process(IARequest $request, ?IAResponse $previousResponse = null): IAResponse
    {
        if (!$previousResponse) {
            return new IAResponse('', 0);
        }

        $this->logger->info('IA Pipeline', [
            'prompt' => $request->prompt,
            'tokens' => $previousResponse->tokensUsed,
        ]);

        return $previousResponse;
    }
}
