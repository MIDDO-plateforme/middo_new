<?php

namespace App\AI\Step;

use App\AI\Pipeline\PipelineContext;
use App\AI\DTO\AIResponse;

interface StepInterface
{
    public function getName(): string;

    public function execute(PipelineContext $context): AIResponse|StepResult;
}
