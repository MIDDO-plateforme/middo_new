<?php

namespace App\KernelPipelines\Steps;

use App\KernelPipelines\PipelineStep;

class ExtractImportantDataStep implements PipelineStep
{
    public function process(array $data): array
    {
        $text = $data['clean_text'] ?? '';

        $data['extracted'] = [
            'dates' => [],
            'numbers' => [],
        ];

        preg_match_all('/\d{2}\/\d{2}\/\d{4}/', $text, $dates);
        preg_match_all('/\d+/', $text, $numbers);

        $data['extracted']['dates'] = $dates[0] ?? [];
        $data['extracted']['numbers'] = $numbers[0] ?? [];

        return $data;
    }
}
