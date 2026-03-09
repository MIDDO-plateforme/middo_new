<?php

namespace App\Infrastructure\IA\Provider;

class AdminKnowledgeProvider
{
    private array $knowledge = [];

    public function __construct()
    {
        // Chargement automatique des fichiers YAML
        $this->loadKnowledgeFile('config/knowledge/admin.yaml');
        $this->loadKnowledgeFile('config/knowledge/business.yaml');
        $this->loadKnowledgeFile('config/knowledge/association.yaml');
        $this->loadKnowledgeFile('config/knowledge/legal.yaml');
    }

    private function loadKnowledgeFile(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }

        $data = yaml_parse_file($path);

        if (is_array($data)) {
            $this->knowledge = array_merge_recursive($this->knowledge, $data);
        }
    }

    /**
     * Fusionne les règles selon le domaine et le pays
     */
    public function merge(?string $domain, ?string $country): array
    {
        $domain = $domain ? strtolower($domain) : null;
        $country = $country ? strtolower($country) : null;

        $result = [
            'organism' => null,
            'pieces' => [],
            'steps' => [],
            'notes' => []
        ];

        // 1) Domaine global (admin, business, association, legal…)
        foreach ($this->knowledge as $group => $items) {
            foreach ($items as $key => $entry) {
                if (strtolower($key) === $domain) {
                    $result = $this->mergeEntry($result, $entry);
                }
            }
        }

        // 2) Domaine + pays (si défini dans YAML)
        if ($country) {
            foreach ($this->knowledge as $group => $items) {
                foreach ($items as $key => $entry) {
                    if (isset($entry['countries'][$country])) {
                        $result = $this->mergeEntry($result, $entry['countries'][$country]);
                    }
                }
            }
        }

        return $result;
    }

    private function mergeEntry(array $base, array $entry): array
    {
        if (isset($entry['organism'])) {
            $base['organism'] = $entry['organism'];
        }

        if (isset($entry['pieces']) && is_array($entry['pieces'])) {
            $base['pieces'] = array_unique(array_merge($base['pieces'], $entry['pieces']));
        }

        if (isset($entry['steps']) && is_array($entry['steps'])) {
            $base['steps'] = array_unique(array_merge($base['steps'], $entry['steps']));
        }

        if (isset($entry['notes']) && is_array($entry['notes'])) {
            $base['notes'] = array_unique(array_merge($base['notes'], $entry['notes']));
        }

        return $base;
    }
}
