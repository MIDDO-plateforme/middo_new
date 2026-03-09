<?php

namespace App\AI\Compiler;

use App\AI\Pipeline\AbstractPipeline;
use App\AI\Step\StepInterface;

final class CompiledPipeline
{
    public function __construct(
        private readonly AbstractPipeline $pipeline,
        private readonly array $steps,
    ) {
    }

    public function getPipeline(): AbstractPipeline
    {
        return $this->pipeline;
    }

    /**
     * @return StepInterface[]
     */
    public function getSteps(): array
    {
        return $this->steps;
    }
}
