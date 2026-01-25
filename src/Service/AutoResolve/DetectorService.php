<?php

namespace App\Service\AutoResolve;

class DetectorService
{
    private LogAnalyzer $logAnalyzer;
    private HealthCheckService $healthCheckService;

    public function __construct(
        LogAnalyzer $logAnalyzer,
        HealthCheckService $healthCheckService
    ) {
        $this->logAnalyzer = $logAnalyzer;
        $this->healthCheckService = $healthCheckService;
    }

    public function scanLogs(): array
    {
        $symfonyLogs = $this->logAnalyzer->readSymfonyLogs();
        $renderLogs = $this->logAnalyzer->readRenderLogs();

        $allLogs = array_merge($symfonyLogs, $renderLogs);

        $errors = $this->logAnalyzer->extractErrors($allLogs);
        $classified = $this->logAnalyzer->classifyErrors($errors);

        return [
            'symfony_logs' => $symfonyLogs,
            'render_logs'  => $renderLogs,
            'errors'       => $errors,
            'classified'   => $classified,
        ];
    }

    public function runHealthChecks(): array
    {
        return $this->healthCheckService->runAll();
    }

    public function detectCriticalIssues(): array
    {
        $logsReport = $this->scanLogs();
        $healthReport = $this->runHealthChecks();

        $criticalErrors = [];
        if (!empty($logsReport['classified'])) {
            foreach ($logsReport['classified'] as $type => $items) {
                if (\in_array($type, ['doctrine', 'database', 'routing', 'cache', 'critical'], true)) {
                    $criticalErrors[$type] = $items;
                }
            }
        }

        $healthIssues = [];
        if (!empty($healthReport['database']) && ($healthReport['database']['status'] ?? null) !== 'OK') {
            $healthIssues['database'] = $healthReport['database'];
        }
        if (!empty($healthReport['api']) && \is_array($healthReport['api'])) {
            foreach ($healthReport['api'] as $endpoint => $result) {
                if (($result['status'] ?? null) !== 'OK') {
                    $healthIssues['api'][$endpoint] = $result;
                }
            }
        }
        if (!empty($healthReport['env']) && ($healthReport['env']['status'] ?? null) !== 'OK') {
            $healthIssues['env'] = $healthReport['env'];
        }

        return [
            'critical_errors' => $criticalErrors,
            'health_issues'   => $healthIssues,
        ];
    }

    public function generateReport(): array
    {
        $logsReport = $this->scanLogs();
        $healthReport = $this->runHealthChecks();
        $critical = $this->detectCriticalIssues();

        return [
            'generated_at' => (new \DateTimeImmutable('now'))->format(\DateTimeInterface::ATOM),
            'logs'         => $logsReport,
            'health'       => $healthReport,
            'critical'     => $critical,
        ];
    }
}
