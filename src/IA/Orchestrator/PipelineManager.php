<?php

namespace App\IA\Orchestrator;

class PipelineManager
{
    private array $steps = [
        'ingestion' => ['active' => false, 'data' => null, 'timestamp' => null],
        'organisation' => ['active' => false, 'data' => null, 'timestamp' => null],
        'analyse' => ['active' => false, 'data' => null, 'timestamp' => null],
        'supervision' => ['active' => false, 'data' => null, 'timestamp' => null],
    ];

    public function activateStep(string $step, array $data = []): void
    {
        if (!isset($this->steps[$step])) {
            return;
        }

        foreach ($this->steps as $key => &$info) {
            $info['active'] = ($key === $step);
        }

        $this->steps[$step]['data'] = $data;
        $this->steps[$step]['timestamp'] = (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM);
    }

    public function getSteps(): array
    {
        return $this->steps;
    }
}
