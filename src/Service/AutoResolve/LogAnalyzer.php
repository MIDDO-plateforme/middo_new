<?php

namespace App\Service\AutoResolve;

class LogAnalyzer
{
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function readSymfonyLogs(): array
    {
        $path = $this->projectDir . '/var/log/prod.log';

        if (!is_file($path) || !is_readable($path)) {
            return [];
        }

        $content = @file_get_contents($path);
        if ($content === false) {
            return [];
        }

        $lines = preg_split('/\R/u', $content);
        if ($lines === false) {
            return [];
        }

        return array_values(array_filter($lines, static fn ($line) => $line !== null && $line !== ''));
    }

    public function readRenderLogs(): array
    {
        return [];
    }

    public function extractErrors(array $logs): array
    {
        $errors = [];

        foreach ($logs as $line) {
            if (
                stripos($line, 'error') !== false ||
                stripos($line, 'critical') !== false ||
                stripos($line, 'exception') !== false
            ) {
                $errors[] = $line;
            }
        }

        return $errors;
    }

    public function classifyErrors(array $errors): array
    {
        $classified = [
            'doctrine' => [],
            'database' => [],
            'routing'  => [],
            'cache'    => [],
            'critical' => [],
            'generic'  => [],
        ];

        foreach ($errors as $line) {
            $lower = strtolower($line);

            if (str_contains($lower, 'doctrine') || str_contains($lower, 'entitymanager')) {
                $classified['doctrine'][] = $line;
            } elseif (str_contains($lower, 'sql') || str_contains($lower, 'database')) {
                $classified['database'][] = $line;
            } elseif (str_contains($lower, 'route') || str_contains($lower, 'routing')) {
                $classified['routing'][] = $line;
            } elseif (str_contains($lower, 'cache')) {
                $classified['cache'][] = $line;
            } elseif (str_contains($lower, 'critical')) {
                $classified['critical'][] = $line;
            } else {
                $classified['generic'][] = $line;
            }
        }

        return $classified;
    }
}
