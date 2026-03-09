<?php

namespace App\Application\IA\Handler;

use App\Domain\IA\Pipeline\VoiceConversationPipeline;
use App\Application\IA\Command\GenericIACommand;

class VoiceTranslationHandler
{
    public function __construct(
        private VoiceConversationPipeline $pipeline
    ) {}

    public function handle(GenericIACommand $command): array
    {
        $response = $this->pipeline->run($command->toRequest());

        return [
            'audio' => $response->text, // audio encodé (base64)
            'tokens' => $response->tokensUsed,
        ];
    }
}
