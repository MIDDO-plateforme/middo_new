<?php

namespace App\KernelPipelines\Steps;

use App\KernelPipelines\PipelineStep;

class CleanTextStep implements PipelineStep
{
    public function process(array $data): array
    {
        $text = $data['text'] ?? '';

        $clean = trim(preg_replace('/\s+/', ' ', $text));

        $data['clean_text'] = $clean;

        return $data;
    }
}
