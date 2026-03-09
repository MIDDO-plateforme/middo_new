<?php

namespace App\Application\IA\Command;

use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IASettings;

class GenericIACommand
{
    public function __construct(
        public string $prompt,
        public ?string $sessionId = null,
        public ?string $targetLanguage = null
    ) {}

    public function toRequest(): IARequest
    {
        return new IARequest(
            prompt: $this->prompt,
            settings: new IASettings(
                sessionId: $this->sessionId,
                targetLanguage: $this->targetLanguage
            )
        );
    }
}
