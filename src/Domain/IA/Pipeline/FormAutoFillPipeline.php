<?php

namespace App\Domain\IA\Pipeline;

use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;
use App\Infrastructure\IA\Provider\AdminKnowledgeProvider;
use App\Infrastructure\IA\Provider\AdminFieldExtractor;

class FormAutoFillPipeline implements IAPipelineEngineInterface
{
    public function __construct(
        private AdminKnowledgeProvider $knowledge,
        private AdminFieldExtractor $fields
    ) {}

    public function run(IARequest $request): IAResponse
    {
        $data = json_decode($request->prompt, true) ?? [];

        $domain = $data['domain'] ?? null;
        $country = $data['country'] ?? null;
        $ocrText = $data['ocr'] ?? null;

        if (!$domain || !$country) {
            return new IAResponse(
                text: "Impossible de pré-remplir : domaine ou pays manquant.",
                tokensUsed: 0
            );
        }

        // 1) Règles du pays + domaine
        $rules = $this->knowledge->merge($domain, $country);

        // 2) Extraction des champs depuis OCR
        $extracted = $ocrText ? $this->fields->extract($ocrText) : [];

        // 3) Construction du JSON de pré-remplissage
        $autofill = [
            'country' => $country,
            'domain' => $domain,
            'organism' => $rules['organism'] ?? null,
            'fields' => [
                'name' => $extracted['name'] ?? null,
                'address' => $extracted['address'] ?? null,
                'id_number' => $extracted['id_number'] ?? null,
                'income' => $extracted['income'] ?? null,
                'dates' => $extracted['dates'] ?? [],
                'amounts' => $extracted['amounts'] ?? [],
            ],
            'missing_fields' => [],
            'notes' => $rules['notes'] ?? [],
        ];

        // 4) Détection des champs manquants
        foreach ($autofill['fields'] as $key => $value) {
            if (!$value || $value === [] || $value === '') {
                $autofill['missing_fields'][] = $key;
            }
        }

        // 5) Format final
        $out  = "Pré-remplissage automatique :\n\n";
        $out .= json_encode($autofill, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return new IAResponse(
            text: $out,
            tokensUsed: 0
        );
    }
}
