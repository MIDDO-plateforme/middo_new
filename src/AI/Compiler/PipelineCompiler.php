<?php

namespace App\AI\Compiler;

use App\AI\Pipeline\AbstractPipeline;
use App\AI\Exception\PipelineException;

final class PipelineCompiler
{
    public function compile(AbstractPipeline $pipeline): CompiledPipeline
    {
        $pipeline->validate();

        $steps = $pipeline->getSteps();

        if (empty($steps)) {
            throw new PipelineException(sprintf('Pipeline "%s" has no steps after validation.', $pipeline->getName()));
        }

        return new CompiledPipeline($pipeline, $steps);
    }
}
