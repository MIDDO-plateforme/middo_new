<?php

namespace App\Domain\IA\Pipeline;

use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;

class PageConsistencyPipeline implements IAPipelineEngineInterface
{
    public function run(IARequest $request): IAResponse
    {
        $data = json_decode($request->prompt, true) ?? [];

        $pages = $data['pages'] ?? [];
        $domain = $data['domain'] ?? null;
        $country = $data['country'] ?? null;

        if (empty($pages)) {
            return new IAResponse(
                text: "Aucune page fournie pour la vérification.",
                tokensUsed: 0
            );
        }

        $report = [
            'total_pages' => count($pages),
            'missing_pages' => [],
            'duplicate_pages' => [],
            'out_of_order_pages' => [],
            'inconsistent_pages' => [],
            'summary' => ''
        ];

        // 1) Détection des pages manquantes (ex: page 1, page 2, page 3…)
        $numbers = [];
        foreach ($pages as $p) {
            if (preg_match('/page\s*(\d+)/i', mb_strtolower($p['text']), $m)) {
                $numbers[] = (int)$m[1];
            }
        }

        if (!empty($numbers)) {
            $min = min($numbers);
            $max = max($numbers);

            for ($i = $min; $i <= $max; $i++) {
                if (!in_array($i, $numbers)) {
                    $report['missing_pages'][] = $i;
                }
            }
        }

        // 2) Détection des doublons
        $counts = array_count_values($numbers);
        foreach ($counts as $num => $count) {
            if ($count > 1) {
                $report['duplicate_pages'][] = $num;
            }
        }

        // 3) Détection des pages hors ordre
        $sorted = $numbers;
        sort($sorted);
        if ($sorted !== $numbers) {
            $report['out_of_order_pages'] = $numbers;
        }

        // 4) Détection des incohérences textuelles
        foreach ($pages as $index => $p) {
            $text = mb_strtolower($p['text']);

            // incohérence : mélange de documents
            if (
                (str_contains($text, 'caf') && str_contains($text, 'cnps')) ||
                (str_contains($text, 'inss') && str_contains($text, 'caf')) ||
                (str_contains($text, 'social security') && str_contains($text, 'cnps'))
            ) {
                $report['inconsistent_pages'][] = "Page " . ($index + 1) . " contient des éléments de plusieurs organismes.";
            }

            // incohérence : changement de domaine
            if (
                (str_contains($text, 'logement') && str_contains($text, 'santé')) ||
                (str_contains($text, 'famille') && str_contains($text, 'impôts'))
            ) {
                $report['inconsistent_pages'][] = "Page " . ($index + 1) . " mélange plusieurs domaines administratifs.";
            }
        }

        // 5) Résumé automatique
        $summary = "Vérification de cohérence documentaire :\n\n";
        $summary .= "- Pages fournies : " . count($pages) . "\n";
        $summary .= "- Pages détectées : " . implode(', ', $numbers) . "\n\n";

        if (!empty($report['missing_pages'])) {
            $summary .= "Pages manquantes : " . implode(', ', $report['missing_pages']) . "\n";
        } else {
            $summary .= "Aucune page manquante détectée.\n";
        }

        if (!empty($report['duplicate_pages'])) {
            $summary .= "Pages en double : " . implode(', ', $report['duplicate_pages']) . "\n";
        } else {
            $summary .= "Aucun doublon détecté.\n";
        }

        if (!empty($report['out_of_order_pages'])) {
            $summary .= "Pages hors ordre : " . implode(', ', $report['out_of_order_pages']) . "\n";
        } else {
            $summary .= "Ordre des pages correct.\n";
        }

        if (!empty($report['inconsistent_pages'])) {
            $summary .= "\nIncohérences détectées :\n";
            foreach ($report['inconsistent_pages'] as $inc) {
                $summary .= "- $inc\n";
            }
        } else {
            $summary .= "\nAucune incohérence textuelle détectée.\n";
        }

        $report['summary'] = $summary;

        return new IAResponse(
            text: $summary,
            tokensUsed: 0
        );
    }
}
