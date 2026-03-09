<?php

namespace App\KernelValidator\Core;

interface ValidatorInterface
{
    public function validate(): ValidationResult;
}
