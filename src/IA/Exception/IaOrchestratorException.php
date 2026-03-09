<?php

namespace App\IA\Exception;

class IaOrchestratorException extends \RuntimeException
{
    public function __construct(
        string $message,
        private array $context = [],
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
