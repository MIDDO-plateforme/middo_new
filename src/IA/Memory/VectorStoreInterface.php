<?php

namespace App\IA\Memory;

interface VectorStoreInterface
{
    /**
     * @param array<int, float> $embedding
     * @param array<string, mixed> $metadata
     */
    public function save(array $embedding, array $metadata): void;

    /**
     * @param array<int, float> $embedding
     * @return array<int, array<string, mixed>>
     */
    public function search(array $embedding, int $topK = 5): array;
}
