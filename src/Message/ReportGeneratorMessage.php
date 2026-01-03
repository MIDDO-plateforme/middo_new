<?php

namespace App\Message;

class ReportGeneratorMessage
{
    private string $reportType;
    private string $format;
    private array $filters;

    public function __construct(string $reportType, string $format = 'pdf', array $filters = [])
    {
        $this->reportType = $reportType;
        $this->format = $format;
        $this->filters = $filters;
    }

    public function getReportType(): string
    {
        return $this->reportType;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }
}