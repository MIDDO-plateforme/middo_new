<?php

namespace App\IA\Agent;

use App\IA\AiKernel;

class TranslationAgent implements IaAgentInterface
{
    public function __construct(private AiKernel $kernel) {}

    public function getName(): string
    {
        return 'translation';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['traduction', 'translate']);
    }

    public function process(string $task, string $input): string
    {
        $prompt = "Traduis ce texte de manière fidèle et claire : $input";
        return $this->kernel->generate($prompt);
    }
}
