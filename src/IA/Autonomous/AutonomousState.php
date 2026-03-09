<?php

namespace App\IA\Autonomous;

class AutonomousState
{
    public function __construct(
        public string $status = 'idle',
        public ?string $currentTask = null,
        public int $iteration = 0,
    ) {
    }
}
