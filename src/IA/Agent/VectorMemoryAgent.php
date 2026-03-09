<?php

namespace App\IA\Agent;

use App\IA\Memory\EmbeddingProvider;
use App\IA\Memory\VectorStoreInterface;

class VectorMemoryAgent implements IaAgentInterface
{
    public function __construct(
        private EmbeddingProvider $embeddingProvider,
        private VectorStoreInterface $store
    ) {}

    public function getName(): string
    {
        return 'vector-memory';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['memoire-vectorielle-ecrire', 'vector-memory-write', 'vector-memory']);
    }

    public function process(string $task, string $input): string
    {
        $embedding = $this->embeddingProvider->embed($input);

        $metadata = [
            'texte' => $input,
            'timestamp' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        ];

        $this->store->save($embedding, $metadata);

        return json_encode(
            [
                'statut' => 'ok',
                'message' => 'Texte stocké en mémoire vectorielle',
                'metadata' => $metadata,
            ],
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        );
    }
}
