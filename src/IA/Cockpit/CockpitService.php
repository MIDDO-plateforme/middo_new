<?php

namespace App\IA\Cockpit;

use App\IA\Cortex\CortexEngine;
use App\IA\Autonomous\AutonomousState;
use App\IA\Vision\VisionPipeline;
use Psr\Log\LoggerInterface;

class CockpitService
{
    public function __construct(
        private CortexEngine $cortexEngine,
        private AutonomousState $autonomousState,
        private VisionPipeline $visionPipeline,
        private LoggerInterface $logger,
    ) {
    }

    public function buildSnapshot(): CockpitSnapshot
    {
        $this->logger->info('[COCKPIT] Building snapshot');

        $cortexState = $this->cortexEngine->getGlobalState();
        $longTermMemory = $this->cortexEngine->getLongTermMemory();

        $autonomous = [
            'status' => $this->autonomousState->status,
            'currentTask' => $this->autonomousState->currentTask,
            'iteration' => $this->autonomousState->iteration,
        ];

        $visionAvailable = true;

        return new CockpitSnapshot(
            cortexState: $cortexState,
            longTermMemory: $longTermMemory,
            autonomousState: $autonomous,
            visionAvailable: $visionAvailable,
        );
    }
}
