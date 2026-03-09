<?php

namespace App\Domain\IA\Pipeline;

use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;
use App\Infrastructure\IA\Provider\AdminKnowledgeProvider;
use App\Infrastructure\IA\Provider\AdminFieldExtractor;
use App\Infrastructure\IA\Provider\DocumentClassifier;

class DocumentRouterPipeline implements IAPipelineEngineInterface
{
    public function __construct(
        private AdminKnowledgeProvider $knowledge,
        private AdminFieldExtractor $fields,
        private DocumentClassifier $classifier
    ) {}

    public function run(IARequest $request): IAResponse
    {
        $data = json_decode($request->prompt, true) ?? [];

        $ocr = $data['ocr'] ?? null;
        $pages = $data['pages'] ?? [];
        $country = $data['country'] ?? null;
        $domain = $data['domain'] ?? null;
        $classification = $data['classification'] ?? null;

        // 1) Classification si absente
        if (!$classification) {
            $classification = $this->classifier->classify($ocr);
        }

        if (!$domain) {
            $domain = $classification['domain'] ?? null;
        }

        if (!$domain || !$country) {
            return new IAResponse(
                text: "Impossible de router le document : domaine ou pays manquant.",
                tokensUsed: 0
            );
        }

        // 2) Règles du KnowledgeLayer
        $rules = $this->knowledge->merge($domain, $country);

        // 3) Extraction des champs
        $extracted = $ocr ? $this->fields->extract($ocr) : [];

        // 4) Analyse des pages
        $pageReport = $this->analyzePages($pages);

        // 5) Statut + notifications
        $status = $this->computeStatus($rules, $extracted, $pageReport);
        $notifications = $this->computeNotifications($rules, $extracted, $pageReport, $status);

        // 6) Rangement (Option E : mois + type + fournisseur)
        $storagePath = $this->computeStoragePath($classification, $extracted);

        // 7) Envoi logique au cabinet comptable
        $sendToCabinet = $this->computeSendToCabinet($classification, $status);

        // 8) Dossier complet
        $dossier = [
            'metadata' => [
                'domain' => $domain,
                'country' => $country,
                'type' => $classification['type'] ?? null,
                'organism' => $rules['organism'] ?? null,
                'generated_at' => date('c'),
            ],
            'classification' => $classification,
            'ocr' => $ocr,
            'extracted_fields' => $extracted,
            'required_pieces' => $rules['pieces'] ?? [],
            'steps' => $rules['steps'] ?? [],
            'notes' => $rules['notes'] ?? [],
            'page_consistency' => $pageReport,
            'status' => $status,
            'notifications' => $notifications,
            'storage' => [
                'path' => $storagePath,
            ],
            'send' => [
                'to_cabinet' => $sendToCabinet,
            ],
        ];

        $out = "Dossier routé et assemblé :\n\n";
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
            'summary' => "Analyse des pages effectuée."
        ];
    }

    private function computeStatus(array $rules, array $fields, array $pages): string
    {
        $missingPieces = $rules['pieces'] ?? [];
        $missingFields = [];

        // On considère que certains champs clés doivent être présents
        foreach (['name', 'address', 'id_number', 'income'] as $key) {
            if (!isset($fields[$key]) || empty($fields[$key])) {
                $missingFields[] = $key;
            }
        }

        $hasMissingPages = !empty($pages['missing_pages']);

        if ($hasMissingPages || !empty($missingPieces) || !empty($missingFields)) {
            return 'incomplet';
        }

        return 'complet';
    }

    private function computeNotifications(array $rules, array $fields, array $pages, string $status): array
    {
        $notifications = [];

        if (!empty($pages['missing_pages'])) {
            $notifications[] = "Pages manquantes : " . implode(', ', $pages['missing_pages']);
        }

        if (!empty($pages['duplicate_pages'])) {
            $notifications[] = "Pages en double : " . implode(', ', $pages['duplicate_pages']);
        }

        if (!empty($pages['out_of_order_pages'])) {
            $notifications[] = "Pages hors ordre : " . implode(', ', $pages['out_of_order_pages']);
        }

        if (!empty($rules['pieces'])) {
            $notifications[] = "Pièces requises : " . implode(', ', $rules['pieces']);
        }

        if ($status === 'complet') {
            $notifications[] = "Dossier complet, prêt à être envoyé.";
        } else {
            $notifications[] = "Dossier incomplet, des éléments sont manquants ou à vérifier.";
        }

        return $notifications;
    }

    private function computeStoragePath(array $classification, array $fields): string
    {
        $now = new \DateTimeImmutable();
        $month = $now->format('Y-m');

        $type = $classification['type'] ?? 'autre';
        $domain = $classification['domain'] ?? 'autre';

        // Fournisseur / client pour les documents comptables
        $supplier = $fields['supplier'] ?? $fields['fournisseur'] ?? null;
        if (!$supplier && isset($fields['name'])) {
            $supplier = $fields['name'];
        }
        if (!$supplier) {
            $supplier = 'inconnu';
        }

        // Option E : mois + type + fournisseur
        if ($domain === 'comptable') {
            return sprintf('comptable/%s/%s/%s', $month, $type, $this->slugify($supplier));
        }

        // Autres domaines : domaine/type
        return sprintf('%s/%s', $domain, $type);
    }

    private function computeSendToCabinet(array $classification, string $status): bool
    {
        // On envoie au cabinet uniquement les documents comptables complets
        if (($classification['domain'] ?? null) === 'comptable' && $status === 'complet') {
            return true;
        }

        return false;
    }

    private function slugify(string $value): string
    {
        $value = mb_strtolower($value);
        $value = preg_replace('/[^a-z0-9]+/i', '-', $value);
        $value = trim($value, '-');
        return $value ?: 'inconnu';
    }
}
