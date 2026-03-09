<?php

namespace App\KernelPipelines\Steps;

use App\KernelPipelines\PipelineStep;

class TranslateStep implements PipelineStep
{
    public function __construct(private string $lang)
    {
    }

    public function process(array $data): array
    {
        $summary = $data['summary'] ?? '';

        $data['translated'] = "[{$this->lang}] $summary";

        return $data;
    }
}
