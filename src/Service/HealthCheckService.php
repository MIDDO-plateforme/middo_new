<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class HealthCheckService
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getSystemStatus(): string
    {
        return 'operational';
    }

    public function getUptime(): int
    {
        return 100;
    }
}