<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class CircuitBreakerService
{
    private LoggerInterface $logger;
    private array $circuits = [];

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getCircuits(): array
    {
        return $this->circuits;
    }
}