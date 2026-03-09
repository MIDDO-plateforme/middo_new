<?php

namespace App\Application\IA\Handler;

use App\Domain\IA\Pipeline\IAPipelineEngineInterface;
use App\Domain\IA\ValueObject\IARequest;
use App\Application\IA\Command\GenerateResponseCommand;
use App\Domain\IA\ValueObject\IASettings;

class GenerateResponseHandler
{
    public function __construct(
        private IAPipelineEngineInterface $pipeline
    ) {}

    public function handle(GenerateResponseCommand $command): array
    {
        $request = new IARequest(
            prompt: $command->prompt,
            settings: new IASettings(
                temperature: $command->temperature,
                maxTokens: $command->maxTokens
            )
        );

        $response = $this->pipeline->run($request);

        return [
            'text' => $response->text,
            'tokens' => $response->tokensUsed,
        ];
    }
}
