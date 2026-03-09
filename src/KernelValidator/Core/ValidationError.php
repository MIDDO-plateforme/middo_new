<?php

namespace App\KernelValidator\Core;

class ValidationError
{
    public function __construct(
        public string $message,
        public ?string $file = null,
        public ?int $line = null
    ) {}
}
