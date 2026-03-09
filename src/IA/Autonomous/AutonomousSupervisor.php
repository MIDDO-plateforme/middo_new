<?php

namespace App\IA\Autonomous;

class AutonomousSupervisor
{
    public function shouldContinue(AutonomousState $state): bool
    {
        return $state->iteration < 5;
    }
}
