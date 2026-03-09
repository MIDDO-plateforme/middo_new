<?php

namespace App\Domain\IA\Pipeline;

use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;
use App\Infrastructure\IA\Provider\AdminHelperProvider;
use App\Infrastructure\IA\Provider\AdminKnowledgeProvider;
use App\Infrastructure\IA\Provider\AdminFieldExtractor;

class AdminHelperPipeline implements IAPipelineEngineInterface
{
    public function __construct(
        private AdminHelperProvider $helper,
        private AdminKnowledgeProvider $knowledge,
        private AdminFieldExtractor $fields
    ) {}

    public function run(IARequest $request): IAResponse
    {
        $data = json_decode($request->prompt, true) ?? [];

        $title = $data['title'] ?? '';
        $country = $data['country'] ?? null;
        $description = $data['description'] ?? null;
        $ocrText = $data['ocr'] ?? null;

        // 1) Analyse générique (titre + pays)
        $analysis = $this->helper->analyze($title, $country, $description);

        // 2) Base mondiale
        $knowledge = $this->knowledge->merge($analysis['domain'], $country);

        // 3) Extraction des champs si OCR fourni
        $extracted = $ocrText ? $this->fields->extract($ocrText) : [];

        $out  = "Démarche : {$title}\n";
        if ($country) {
            $out .= "Pays : {$country}\n";
        }
        $out .= "Domaine : {$analysis['domain']}\n";

        if ($knowledge['organism']) {
            $out .= "Organisme : {$knowledge['organism']}\n";
        }

        if ($description) {
            $out .= "\nCe que tu as décrit :\n{$description}\n";
        }

        if ($ocrText) {
            $out .= "\nChamps détectés dans le document :\n";
            foreach ($extracted as $key => $value) {
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
                $out .= "- {$key} : " . ($value ?: 'non détecté') . "\n";
            }
        }

        $out .= "\nPièces nécessaires :\n";
        foreach ($knowledge['pieces'] as $p) {
            $out .= "- {$p}\n";
        }

        $out .= "\nÉtapes générales :\n";
        foreach ($knowledge['steps'] as $s) {
            $out .= "- {$s}\n";
        }

        if (!empty($knowledge['notes'])) {
            $out .= "\nNotes spécifiques au pays :\n";
            foreach ($knowledge['notes'] as $n) {
                $out .= "- {$n}\n";
            }
        }

        return new IAResponse(
            text: $out,
            tokensUsed: 0
        );
    }
}
