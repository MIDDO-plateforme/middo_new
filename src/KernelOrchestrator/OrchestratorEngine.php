<?php

namespace App\KernelOrchestrator;

use App\KernelValidator\KernelValidator;
use App\KernelCompiler\CompilerEngine;

class OrchestratorEngine
{
    public function __construct(
        private KernelValidator $validator,
        private CompilerEngine $compiler
    ) {}

    public function orchestrate(): OrchestratorState
    {
        $validation = $this->validator->run();
        $compiled = $this->compiler->compile($validation);

        return new OrchestratorState(
            timestamp: time(),
            validation: $validation,
            compiled: $compiled
        );
    }
}
