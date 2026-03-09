<?php

namespace App\IA\Cockpit;

class CockpitSnapshot
{
    public function __construct(
        public array $cortexState,
        public array $longTermMemory,
        public array $autonomousState,
        public bool $visionAvailable,
    ) {
    }

    public function toArray(): array
    {
        return [
            'cortex_state' => $this->cortexState,
            'long_term_memory' => $this->longTermMemory,
            'autonomous_state' => $this->autonomousState,
            'vision_available' => $this->visionAvailable,
        ];
    }
}
