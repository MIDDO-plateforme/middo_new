<?php

namespace App\IA\Cortex;

use Psr\Log\LoggerInterface;

class CortexMonitor
{
    public function __construct(
        private CortexEngine $cortexEngine,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Logue un événement cognitif (ce que fait / pense l’IA).
     */
    public function logCognitiveEvent(string $event, array $context = []): void
    {
        $state = $this->cortexEngine->getGlobalState();

        $this->logger->info('[CORTEX][EVENT] ' . $event, [
            'context' => $context,
            'state' => $state,
        ]);
    }

    /**
     * Retourne une mini‑map cognitive de l’IA.
     */
    public function getMiniMap(): array
    {
        return [
            'global_state' => $this->cortexEngine->getGlobalState(),
            'long_term_memory_keys' => array_keys($this->cortexEngine->getLongTermMemory()),
        ];
    }

    /**
     * Logue un snapshot complet du Cortex.
     */
    public function logSnapshot(string $label = 'snapshot'): void
    {
        $this->logger->info('[CORTEX][SNAPSHOT] ' . $label, [
            'global_state' => $this->cortexEngine->getGlobalState(),
            'long_term_memory' => $this->cortexEngine->getLongTermMemory(),
        ]);
    }
}
