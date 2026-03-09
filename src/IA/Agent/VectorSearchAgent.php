<?php

namespace App\IA\Agent;

use App\IA\Memory\EmbeddingProvider;
use App\IA\Memory\VectorStoreInterface;

class VectorSearchAgent implements IaAgentInterface
{
    public function __construct(
        private EmbeddingProvider $embeddingProvider,
        private VectorStoreInterface $store
    ) {}

    public function getName(): string
    {
        return 'vector-search';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['memoire-vectorielle-rechercher', 'vector-memory-search', 'vector-search']);
    }

    public function process(string $task, string $input): string
    {
        $embedding = $this->embeddingProvider->embed($input);
        $results = $this->store->search($embedding, 5);

        return json_encode(
            [
                'requete' => $input,
                'resultats' => $results,
            ],
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        );
    }
}
