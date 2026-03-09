<?php

namespace App\Domain\IA\Pipeline;

use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;
use App\Infrastructure\IA\Provider\AdminKnowledgeProvider;
use App\Infrastructure\IA\Provider\AdminFieldExtractor;

class TaskGuidancePipeline implements IAPipelineEngineInterface
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
        $step = $data['step'] ?? 1;

        if (!$domain || !$country) {
            return new IAResponse(
                text: "Je ne peux pas guider la démarche : domaine ou pays manquant.",
                tokensUsed: 0
            );
        }

        $rules = $this->knowledge->merge($domain, $country);
        $extracted = $ocrText ? $this->fields->extract($ocrText) : [];
        $steps = $rules['steps'] ?? [];

        if (empty($steps)) {
            return new IAResponse(
                text: "Aucune étape trouvée pour cette démarche.",
                tokensUsed: 0
            );
        }

        $currentStep = $steps[$step - 1] ?? null;

        if (!$currentStep) {
            return new IAResponse(
                text: "Toutes les étapes sont terminées. La démarche est complète.",
                tokensUsed: 0
            );
        }

        $analysis = $this->analyzeStep($currentStep, $extracted);

        $out  = "Étape {$step} : {$currentStep}\n\n";
        $out .= $analysis;

        if (isset($steps[$step])) {
            $out .= "\nÉtape suivante disponible : " . $steps[$step] . "\n";
            $out .= "Pour continuer, envoie : { \"domain\": \"$domain\", \"country\": \"$country\", \"step\": " . ($step + 1) . " }\n";
        } else {
            $out .= "\nLa démarche est terminée.\n";
        }

        return new IAResponse(
            text: $out,
            tokensUsed: 0
        );
    }

    private function analyzeStep(string $step, array $extracted): string
    {
        $text = mb_strtolower($step);
        $out = "";

        if (str_contains($text, 'identité')) {
            $out .= "Vérification de l'identité :\n";
            $out .= "- Nom détecté : " . ($extracted['name'] ?? 'non détecté') . "\n";
            $out .= "- Adresse détectée : " . ($extracted['address'] ?? 'non détectée') . "\n";
        }

        if (str_contains($text, 'revenu')) {
            $out .= "Vérification des revenus :\n";
            $out .= "- Revenu détecté : " . ($extracted['income'] ?? 'non détecté') . "\n";
            $out .= "- Montants trouvés : " . (empty($extracted['amounts']) ? 'aucun' : implode(', ', $extracted['amounts'])) . "\n";
        }

        if (str_contains($text, 'numéro') || str_contains($text, 'identifiant')) {
            $out .= "Numéro administratif : " . ($extracted['id_number'] ?? 'non détecté') . "\n";
        }

        if ($out === "") {
            $out = "Cette étape ne nécessite pas de vérification automatique. Suis simplement l’instruction indiquée.";
        }

        return $out;
    }
}
