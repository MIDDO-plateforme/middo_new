<?php

namespace App\IA\Agent;

use App\IA\AiKernel;

class EducationAgent implements IaAgentInterface
{
    public function __construct(private AiKernel $kernel) {}

    public function getName(): string
    {
        return 'education';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['cours', 'explication', 'apprentissage']);
    }

    public function process(string $task, string $input): string
    {
        $prompt = "Tu es un professeur pédagogue. Explique clairement : $input";
        return $this->kernel->generate($prompt);
    }
}
