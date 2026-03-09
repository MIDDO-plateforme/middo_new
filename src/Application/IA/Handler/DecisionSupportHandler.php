<?php

namespace App\Application\IA\Handler;

use App\Domain\IA\Pipeline\DecisionSupportPipeline;
use App\Application\IA\Command\GenericIACommand;

class DecisionSupportHandler
{
    public function __construct(
        private DecisionSupportPipeline $pipeline
    ) {}

    public function handle(GenericIACommand $command): array
    {
        $response = $this->pipeline->run($command->toRequest());

        return [
            'decision' => $response->text,
            'tokens' => $response->tokensUsed,
        ];
    }
}
