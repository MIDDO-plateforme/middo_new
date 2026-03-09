<?php

namespace App\KernelCompiler;

use App\KernelValidator\Core\ValidationResult;

class CompilerEngine
{
    public function compile(ValidationResult $result): array
    {
        return [
            'timestamp' => time(),
            'errors' => array_map(fn($e) => $e->message, $result->errors),
            'warnings' => array_map(fn($w) => $w->message, $result->warnings),
            'infos' => array_map(fn($i) => $i->message, $result->infos),
        ];
    }
}
