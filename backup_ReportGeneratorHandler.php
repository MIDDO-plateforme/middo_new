<?php

namespace App\Handler;

use App\Message\ReportGeneratorMessage;
use App\Service\ReportService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ReportGeneratorHandler
{
    private ReportService $reportService;
    private ?LoggerInterface $logger;

    public function __construct(
        ReportService $reportService,
        ?LoggerInterface $logger = null
    ) {
        $this->reportService = $reportService;
        $this->logger = $logger;
    }

    public function __invoke(ReportGeneratorMessage $message): void
    {
        $reportType = $message->getReportType();
        $format = $message->getFormat();
        $filters = $message->getFilters();

        $this->logger?->info('Generating report', [
            'type' => $reportType,
            'format' => $format,
            'filters' => $filters,
        ]);

        try {
            // Logique de génération de rapport via ReportService
            $reportData = $this->reportService->generateCustomReport([
                'type' => $reportType,
                'format' => $format,
                'filters' => $filters,
            ]);

            $this->logger?->info('Report generated successfully', [
                'type' => $reportType,
                'format' => $format,
            ]);
        } catch (\Exception $e) {
            $this->logger?->error('Report generation failed', [
                'type' => $reportType,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}