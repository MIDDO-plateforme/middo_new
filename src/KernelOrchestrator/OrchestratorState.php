<?php

namespace App\KernelOrchestrator;

use App\KernelValidator\Core\ValidationResult;

class OrchestratorState
{
    public function __construct(
        public int $timestamp,
        public ValidationResult $validation,
        public array $compiled
    ) {}

    public function toArray(): array
    {
        return [
            'timestamp' => $this->timestamp,
            'validation' => [
                'errors' => array_map(fn($e) => $e->message, $this->validation->errors),
                'warnings' => array_map(fn($w) => $w->message, $this->validation->warnings),
                'infos' => array_map(fn($i) => $i->message, $this->validation->infos),
            ],
            'compiled' => $this->compiled
        ];
    }
}
