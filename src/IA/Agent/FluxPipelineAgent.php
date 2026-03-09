<?php

namespace App\IA\Agent;

class FluxPipelineAgent implements IaAgentInterface
{
    public function __construct(
        private FluxIngestionAgent $ingestion,
        private FluxOrganizerAgent $organizer,
        private FluxAnalyticsAgent $analytics,
        private FluxSupervisorAgent $supervisor
    ) {}

    public function getName(): string
    {
        return 'flux-pipeline';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['flux-pipeline', 'pipeline-flux', 'traitement-flux-complet']);
    }

    public function process(string $task, string $input): string
    {
        $ingested = $this->ingestion->process('flux-ingest', $input);
        $organized = $this->organizer->process('flux-organize', $ingested);
        $analysed = $this->analytics->process('flux-analyse', $organized);
        $supervised = $this->supervisor->process('flux-supervision', $organized);

        $result = [
            'ingestion' => json_decode($ingested, true),
            'organisation' => json_decode($organized, true),
            'analyse' => json_decode($analysed, true),
            'supervision' => json_decode($supervised, true),
        ];

        return json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
