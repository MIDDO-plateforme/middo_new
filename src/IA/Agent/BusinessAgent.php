<?php

namespace App\IA\Agent;

use App\IA\AiKernel;

class BusinessAgent implements IaAgentInterface
{
    public function __construct(private AiKernel $kernel) {}

    public function getName(): string
    {
        return 'business';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['entreprise', 'startup', 'business']);
    }

    public function process(string $task, string $input): string
    {
        $prompt = "Tu es un expert en stratégie d'entreprise. Analyse : $input";
        return $this->kernel->generate($prompt);
    }
}
