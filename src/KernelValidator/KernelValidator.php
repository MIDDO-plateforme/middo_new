<?php

namespace App\KernelValidator;

use App\KernelValidator\Core\ValidatorInterface;
use App\KernelValidator\Core\ValidationResult;

class KernelValidator
{
    /** @var ValidatorInterface[] */
    private array $scanners = [];

    public function addScanner(ValidatorInterface $scanner): void
    {
        $this->scanners[] = $scanner;
    }

    public function run(): ValidationResult
    {
        $result = new ValidationResult();

        foreach ($this->scanners as $scanner) {
            $scan = $scanner->validate();

            $result->errors = array_merge($result->errors, $scan->errors);
            $result->warnings = array_merge($result->warnings, $scan->warnings);
            $result->infos = array_merge($result->infos, $scan->infos);
        }

        return $result;
    }
}
