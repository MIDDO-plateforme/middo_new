<?php

namespace App\IA\Autonomous;

class AutonomousTask
{
    public function __construct(
        public string $description,
        public array $context = [],
    ) {
    }
}
