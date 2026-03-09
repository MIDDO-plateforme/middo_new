<?php

namespace App\IA\Cortex;

class CortexMemoryLoader
{
    public function __construct(
        private CortexMemorySynchronizer $synchronizer,
    ) {
    }

    /**
     * Charge la mémoire persistante dans le Cortex au démarrage.
     */
    public function boot(): void
    {
        $this->synchronizer->loadAll();
    }

    /**
     * Sauvegarde la mémoire longue en base.
     */
    public function save(): void
    {
        $this->synchronizer->persistAll();
    }
}
