<?php

namespace App\AI\Pipeline;

use App\AI\Step\StepInterface;
use App\AI\Exception\PipelineException;

abstract class AbstractPipeline
{
    /**
     * @return StepInterface[]
     */
    abstract protected function getSteps(): array;

    public function getName(): string
    {
        return static::class;
    }

    public function buildContext(array $input): PipelineContext
    {
        return new PipelineContext($input);
    }

    public function validate(): void
    {
        $steps = $this->getSteps();

        if (empty($steps)) {
            throw new PipelineException(sprintf('Pipeline "%s" has no steps.', $this->getName()));
        }
    }
}
