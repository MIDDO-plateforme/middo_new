<?php

namespace App\Service\SOIA;

use Psr\Log\LoggerInterface;

class CircuitBreakerService
{
    private LoggerInterface $logger;
    private array $circuits = [];
    private const FAILURE_THRESHOLD = 5;
    private const TIMEOUT = 60;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function canExecute(string $apiName): bool
    {
        if (!isset($this->circuits[$apiName])) {
            $this->circuits[$apiName] = [
                'state' => 'CLOSED',
                'failures' => 0,
                'last_failure' => null
            ];
        }

        $circuit = &$this->circuits[$apiName];

        if ($circuit['state'] === 'OPEN') {
            if (time() - $circuit['last_failure'] > self::TIMEOUT) {
                $circuit['state'] = 'HALF_OPEN';
                $this->logger->info("Circuit [{$apiName}] passé en HALF_OPEN");
            } else {
                return false;
            }
        }

        return true;
    }

    public function recordSuccess(string $apiName): void
    {
        if (isset($this->circuits[$apiName])) {
            $this->circuits[$apiName]['failures'] = 0;
            $this->circuits[$apiName]['state'] = 'CLOSED';
            $this->logger->info("Circuit [{$apiName}] : Succès enregistré");
        }
    }

    public function recordFailure(string $apiName, string $error): void
    {
        if (!isset($this->circuits[$apiName])) {
            $this->circuits[$apiName] = [
                'state' => 'CLOSED',
                'failures' => 0,
                'last_failure' => null
            ];
        }

        $circuit = &$this->circuits[$apiName];
        $circuit['failures']++;
        $circuit['last_failure'] = time();

        if ($circuit['failures'] >= self::FAILURE_THRESHOLD) {
            $circuit['state'] = 'OPEN';
            $this->logger->error("Circuit [{$apiName}] OUVERT après {$circuit['failures']} échecs", [
                'error' => $error
            ]);
        }
    }

    public function getState(string $apiName): string
    {
        return $this->circuits[$apiName]['state'] ?? 'CLOSED';
    }

    public function getAllCircuits(): array
    {
        return $this->circuits;
    }
}