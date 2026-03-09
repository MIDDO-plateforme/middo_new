<?php

namespace App\Application\IA\Handler;

use App\Domain\IA\Pipeline\RiskAnalysisPipeline;
use App\Application\IA\Command\GenericIACommand;

class RiskAnalysisHandler
{
    public function __construct(
        private RiskAnalysisPipeline $pipeline
    ) {}

    public function handle(GenericIACommand $command): array
    {
        $response = $this->pipeline->run($command->toRequest());

        return [
            'analysis' => $response->text,
            'tokens' => $response->tokensUsed,
        ];
    }
}

