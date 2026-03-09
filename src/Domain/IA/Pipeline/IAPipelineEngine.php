<?php

namespace App\Domain\IA\Pipeline;

use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;

class IAPipelineEngine implements IAPipelineEngineInterface
{
    /**
     * @param IAPipelineStepInterface[] $steps
     */
    public function __construct(
        private array $steps
    ) {}

    public function run(IARequest $request): IAResponse
    {
        $response = null;

        foreach ($this->steps as $step) {
            $response = $step->process($request, $response);
        }

        if (!$response instanceof IAResponse) {
            throw new \RuntimeException('Pipeline IA: aucune réponse finale produite.');
        }

        return $response;
    }
}
