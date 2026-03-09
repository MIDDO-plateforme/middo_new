<?php

namespace App\KernelPipelines;

interface PipelineStep
{
    public function process(array $data): array;
}
