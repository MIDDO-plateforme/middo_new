<?php

namespace App\IA\Cortex;

class CortexOrchestrator
{
    public function __construct(
        private CortexEngine $cortexEngine,
        private CortexMonitor $cortexMonitor,
    ) {
    }

    /**
     * Déclenche une mise à jour complète du Cortex :
     * - auto‑régulation
     * - mise à jour de l’état global
     * - log cognitif
     */
    public function tick(string $reason = 'system'): void
    {
        // Auto‑régulation interne
        $this->cortexEngine->autoRegulate();

        // Log de l’événement
        $this->cortexMonitor->logCognitiveEvent('tick', [
            'reason' => $reason,
        ]);
    }

    /**
     * Enregistre une action effectuée par l’IA.
     */
    public function registerAction(string $action): void
    {
        $this->cortexEngine->setLastAction($action);

        $this->cortexMonitor->logCognitiveEvent('action', [
            'action' => $action,
        ]);
    }

    /**
     * Enregistre une information dans la mémoire longue.
     */
    public function remember(string $key, mixed $value): void
    {
        $this->cortexEngine->remember($key, $value);

        $this->cortexMonitor->logCognitiveEvent('memory_update', [
            'key' => $key,
            'value' => $value,
        ]);
    }

    /**
     * Retourne un snapshot complet du Cortex.
     */
    public function snapshot(): array
    {
        $this->cortexMonitor->logSnapshot('manual_snapshot');

        return [
            'global_state' => $this->cortexEngine->getGlobalState(),
            'long_term_memory' => $this->cortexEngine->getLongTermMemory(),
            'mini_map' => $this->cortexMonitor->getMiniMap(),
        ];
    }
}
