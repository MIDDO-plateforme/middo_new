<?php

namespace App\IA\Autonomous;

class AutonomousScheduler
{
    public function nextTask(): AutonomousTask
    {
        return new AutonomousTask('Analyse du contexte global');
    }
}
