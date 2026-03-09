<?php

namespace App\KernelValidator\Reports;

use App\KernelValidator\Core\ValidationResult;
use App\KernelValidator\Core\ValidationError;

class ReportFormatter
{
    public function format(ValidationResult $result): string
    {
        $lines = [];

        foreach ($result->errors as $e) {
            $lines[] = "[ERROR] {$e->message} ({$e->file})";
        }

        foreach ($result->warnings as $w) {
            $lines[] = "[WARN] {$w->message} ({$w->file})";
        }

        if (empty($lines)) {
            $lines[] = "[OK] Aucun problème détecté.";
        }

        return implode(PHP_EOL, $lines);
    }
}
