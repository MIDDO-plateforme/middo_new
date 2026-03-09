<?php

namespace App\KernelValidator\Reports;

use App\KernelValidator\Core\ValidationResult;

class ValidatorReport
{
    public function __construct(public ValidationResult $result) {}
}
