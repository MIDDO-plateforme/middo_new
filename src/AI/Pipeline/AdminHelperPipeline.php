<?php

namespace App\AI\Pipeline;

use App\AI\Step\StepInterface;
use App\AI\Step\ExtractTextStep;
use App\AI\Step\AnalyzeProcedureStep;
use App\AI\Step\GenerateInstructionsStep;
use App\AI\Step\GenerateFormsStep;
use App\AI\Provider\ProviderRouter;

final class AdminHelperPipeline extends AbstractPipeline
{
    public function __construct(
        private ProviderRouter $router
    ) {}

    protected function getSteps(): array
    {
        return [
            new ExtractTextStep(),
            new AnalyzeProcedureStep($this->router),
            new GenerateInstructionsStep($this->router),
            new GenerateFormsStep($this->router),
        ];
    }
}
