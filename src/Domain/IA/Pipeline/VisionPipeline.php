<?php

namespace App\Domain\IA\Pipeline;

use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;
use App\Infrastructure\IA\Provider\VisionProvider;
use App\Infrastructure\IA\Provider\AdminDocumentClassifier;
use App\Infrastructure\IA\Provider\AdminFieldExtractor;

class VisionPipeline implements IAPipelineEngineInterface
{
    public function __construct(
        private VisionProvider $vision,
        private AdminDocumentClassifier $classifier,
        private AdminFieldExtractor $fields
    ) {}

    public function run(IARequest $request): IAResponse
    {
        $imageBase64 = $request->prompt;

        // 1) Vision → OCR + description
        $visionText = $this->vision->analyze($imageBase64, 'Analyse ce document administratif.');

        // 2) Classification (pays + domaine)
        $classification = $this->classifier->classify($visionText);

        // 3) Extraction des champs (nom, adresse, numéro, etc.)
        $extracted = $this->fields->extract($visionText);

        $out  = "Analyse du document :\n";
        $out .= $visionText . "\n\n";

        $out .= "Classification :\n";
        $out .= "- Pays détecté : " . ($classification['country'] ?? 'inconnu') . "\n";
        $out .= "- Domaine détecté : " . ($classification['domain'] ?? 'inconnu') . "\n\n";

        $out .= "Champs extraits :\n";
        foreach ($extracted as $key => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $out .= "- {$key} : " . ($value ?: 'non détecté') . "\n";
        }

        return new IAResponse(
            text: $out,
            tokensUsed: 0
        );
    }
}
