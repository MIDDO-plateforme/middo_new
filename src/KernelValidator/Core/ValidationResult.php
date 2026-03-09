<?php

namespace App\KernelValidator\Core;

class ValidationResult
{
    public function __construct(
        public array $errors = [],
        public array $warnings = [],
        public array $infos = []
    ) {}

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}
