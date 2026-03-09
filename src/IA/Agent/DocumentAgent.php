<?php

namespace App\IA\Agent;

use App\IA\AiKernel;

class DocumentAgent implements IaAgentInterface
{
    public function __construct(private AiKernel $kernel) {}

    public function getName(): string
    {
        return 'document';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['document', 'résumé', 'analyse']);
    }

    public function process(string $task, string $input): string
    {
        $prompt = "Analyse ou résume ce document de manière structurée : $input";
        return $this->kernel->generate($prompt);
    }
}
