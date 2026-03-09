<?php

namespace App\IA\Autonomous;

use App\IA\Cortex\CortexEngine;
use App\IA\Cortex\CortexMonitor;
use App\IA\Cortex\CortexOrchestrator;

class AutonomousLoop
{
    public function __construct(
        private CortexEngine $cortex,
        private CortexMonitor $monitor,
        private CortexOrchestrator $orchestrator,
    ) {
    }

    public function run(AutonomousState $state, AutonomousTask $task): AutonomousState
    {
        $state->status = 'running';
        $state->currentTask = $task->description;
        $state->iteration++;

        $this->monitor->logCognitiveEvent('autonomous_iteration', [
            'task' => $task->description,
            'iteration' => $state->iteration,
        ]);

        $this->orchestrator->registerAction('autonomous_step');
        $this->orchestrator->tick('autonomous_loop');

        return $state;
    }
}
