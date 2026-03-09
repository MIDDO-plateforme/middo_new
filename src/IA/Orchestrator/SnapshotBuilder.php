<?php

namespace App\IA\Orchestrator;

class SnapshotBuilder
{
    public function buildSnapshot(
        PipelineManager $pipelineManager,
        AgentManager $agentManager,
        MemoryManager $memoryManager,
        array $cortexState = [],
        ?string $lastFlux = null
    ): array {
        return [
            'pipeline' => $pipelineManager->getSteps(),
            'cortex_state' => [
                'agents' => $agentManager->getAgentsState(),
                'internal' => $cortexState,
            ],
            'memory' => $memoryManager->getMemory(),
            'last_flux' => $lastFlux ? ['content' => $lastFlux] : null,
        ];
    }
}
