<?php

namespace App\KernelPipelines\Steps;

use App\KernelPipelines\PipelineStep;

class SummarizeStep implements PipelineStep
{
    public function process(array $data): array
    {
        $type = $data['document_type'] ?? 'inconnu';

        $data['summary'] = "Document détecté : $type.";

        return $data;
    }
}
