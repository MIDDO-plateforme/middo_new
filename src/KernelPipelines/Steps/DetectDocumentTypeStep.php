<?php

namespace App\KernelPipelines\Steps;

use App\KernelPipelines\PipelineStep;

class DetectDocumentTypeStep implements PipelineStep
{
    public function process(array $data): array
    {
        $text = strtolower($data['clean_text'] ?? '');

        $type = 'inconnu';

        if (str_contains($text, 'caf')) $type = 'attestation_caf';
        if (str_contains($text, 'scolarité')) $type = 'certificat_scolarite';
        if (str_contains($text, 'impôt')) $type = 'avis_imposition';
        if (str_contains($text, 'facture')) $type = 'facture';

        $data['document_type'] = $type;

        return $data;
    }
}
