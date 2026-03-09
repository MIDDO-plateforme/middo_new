<?php

namespace App\Domain\IA\Pipeline;

use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;
use App\Infrastructure\IA\Provider\AdminKnowledgeProvider;
use App\Infrastructure\IA\Provider\AdminFieldExtractor;

class DocumentAssemblerPipeline implements IAPipelineEngineInterface
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
        $ocr = $data['ocr'] ?? null;
        $pages = $data['pages'] ?? [];
        $classification = $data['classification'] ?? [];
        $autofill = $data['autofill'] ?? [];

        if (!$domain || !$country) {
            return new IAResponse(
                text: "Impossible d’assembler le dossier : domaine ou pays manquant.",
                tokensUsed: 0
            );
        }

        // 1) Règles du KnowledgeLayer
        $rules = $this->knowledge->merge($domain, $country);

        // 2) Extraction des champs depuis OCR
        $extracted = $ocr ? $this->fields->extract($ocr) : [];

        // 3) Analyse des pages (cohérence)
        $pageReport = $this->analyzePages($pages);

        // 4) Construction du dossier complet
        $dossier = [
            'metadata' => [
                'domain' => $domain,
                'country' => $country,
                'organism' => $rules['organism'] ?? null,
                'generated_at' => date('c'),
            ],

            'classification' => $classification,

            'ocr' => $ocr,

            'extracted_fields' => $extracted,

            'autofill' => $autofill,

            'required_pieces' => $rules['pieces'] ?? [],

            'steps' => $rules['steps'] ?? [],

            'notes' => $rules['notes'] ?? [],

            'page_consistency' => $pageReport,

            'summary' => $this->generateSummary($domain, $country, $extracted, $pageReport)
        ];

        $out = "Dossier complet généré :\n\n";
        $out .= json_encode($dossier, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return new IAResponse(
            text: $out,
            tokensUsed: 0
        );
    }

    private function analyzePages(array $pages): array
    {
        if (empty($pages)) {
            return [
                'total_pages' => 0,
                'missing_pages' => [],
                'duplicate_pages' => [],
                'out_of_order_pages' => [],
                'inconsistent_pages' => [],
                'summary' => "Aucune page fournie."
            ];
        }

        $numbers = [];
        foreach ($pages as $p) {
            if (isset($p['text']) && preg_match('/page\s*(\d+)/i', mb_strtolower($p['text']), $m)) {
                $numbers[] = (int)$m[1];
            }
        }

        $missing = [];
        if (!empty($numbers)) {
            $min = min($numbers);
            $max = max($numbers);
            for ($i = $min; $i <= $max; $i++) {
                if (!in_array($i, $numbers)) {
                    $missing[] = $i;
                }
            }
        }

        $duplicates = [];
        $counts = array_count_values($numbers);
        foreach ($counts as $num => $count) {
            if ($count > 1) {
                $duplicates[] = $num;
            }
        }

        $sorted = $numbers;
        sort($sorted);
        $outOfOrder = ($sorted !== $numbers) ? $numbers : [];

        return [
            'total_pages' => count($pages),
            'missing_pages' => $missing,
            'duplicate_pages' => $duplicates,
            'out_of_order_pages' => $outOfOrder,
            'inconsistent_pages' => [],
            'summary' => "Analyse des pages effectuée."
        ];
    }

    private function generateSummary(string $domain, string $country, array $fields, array $pages): string
    {
        $out = "Résumé du dossier :\n";
        $out .= "- Domaine : $domain\n";
        $out .= "- Pays : $country\n";

        if (!empty($fields)) {
            $out .= "- Champs détectés : " . implode(', ', array_keys($fields)) . "\n";
        }

        if (!empty($pages['missing_pages'])) {
            $out .= "- Pages manquantes : " . implode(', ', $pages['missing_pages']) . "\n";
        }

        if (!empty($pages['duplicate_pages'])) {
            $out .= "- Pages en double : " . implode(', ', $pages['duplicate_pages']) . "\n";
        }

        if (!empty($pages['out_of_order_pages'])) {
            $out .= "- Pages hors ordre : " . implode(', ', $pages['out_of_order_pages']) . "\n";
        }

        return $out;
    }
}
