<?php

namespace App\AI\Orchestrator;

use App\AI\Compiler\CompiledPipeline;
use App\AI\Pipeline\PipelineContext;
use App\AI\Step\StepInterface;
use App\AI\Step\StepResult;
use App\AI\Exception\OrchestratorException;

final class Orchestrator
{
    public function run(CompiledPipeline $compiled, array $input = []): PipelineContext
    {
        $pipeline = $compiled->getPipeline();
        $context = $pipeline->buildContext($input);

        foreach ($compiled->getSteps() as $step) {
            if (!$step instanceof StepInterface) {
                throw new OrchestratorException('Invalid step in compiled pipeline.');
            }

            $result = $step->execute($context);

            if ($result instanceof StepResult) {
                $context->set($result->getName(), $result->getData());
            }
        }

        return $context;
    }
}
