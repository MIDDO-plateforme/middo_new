<?php

namespace App\IA\Memory;

class VectorStoreLocal implements VectorStoreInterface
{
    /**
     * @var array<int, array<string, mixed>>
     */
    private array $items = [];

    public function save(array $embedding, array $metadata): void
    {
        $this->items[] = [
            'id' => count($this->items) + 1,
            'embedding' => $embedding,
            'metadata' => $metadata,
        ];
    }

    public function search(array $embedding, int $topK = 5): array
    {
        $scored = [];

        foreach ($this->items as $item) {
            $score = $this->cosineSimilarity($embedding, $item['embedding']);
            $scored[] = [
                'score' => $score,
                'id' => $item['id'],
                'metadata' => $item['metadata'],
            ];
        }

        usort($scored, static fn (array $a, array $b): int => $b['score'] <=> $a['score']);

        return array_slice($scored, 0, $topK);
    }

    /**
     * @param array<int, float> $a
     * @param array<int, float> $b
     */
    private function cosineSimilarity(array $a, array $b): float
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        $len = min(count($a), count($b));

        for ($i = 0; $i < $len; $i++) {
            $dot += $a[$i] * $b[$i];
            $normA += $a[$i] ** 2;
            $normB += $b[$i] ** 2;
        }

        if ($normA == 0.0 || $normB == 0.0) {
            return 0.0;
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }
}
