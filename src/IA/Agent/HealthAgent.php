<?php

namespace App\IA\Agent;

use App\IA\AiKernel;

class HealthAgent implements IaAgentInterface
{
    public function __construct(private AiKernel $kernel) {}

    public function getName(): string
    {
        return 'health';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['santé', 'symptômes', 'bien-être']);
    }

    public function process(string $task, string $input): string
    {
        $prompt = "Tu es un assistant santé. Donne des informations générales, jamais de diagnostic. Sujet : $input";
        return $this->kernel->generate($prompt);
    }
}
