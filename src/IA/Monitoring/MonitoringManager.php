<?php

namespace App\IA\Monitoring;

use Psr\Log\LoggerInterface;

class MonitoringManager
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function logRequest(
        string $providerName,
        float $durationMs,
        bool $success,
        ?string $errorMessage = null
    ): void {
        $context = [
            'provider' => $providerName,
            'duration_ms' => $durationMs,
            'success' => $success,
        ];

        if ($errorMessage !== null) {
            $context['error'] = $errorMessage;
        }

        $this->logger->info('ia.request', $context);
    }
}
