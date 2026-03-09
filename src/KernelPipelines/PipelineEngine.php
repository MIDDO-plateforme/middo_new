<?php

namespace App\KernelPipelines;

class PipelineEngine
{
    public function execute(Pipeline $pipeline, array $input): array
    {
        return $pipeline->run($input);
    }
}
