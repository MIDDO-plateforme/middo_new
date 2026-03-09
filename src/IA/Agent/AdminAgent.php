<?php

namespace App\IA\Agent;

use App\IA\AiKernel;

class AdminAgent implements IaAgentInterface
{
    public function __construct(private AiKernel $kernel) {}

    public function getName(): string
    {
        return 'admin';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['administratif', 'formulaire', 'démarche']);
    }

    public function process(string $task, string $input): string
    {
        $prompt = "Tu es un expert administratif. Aide l'utilisateur pour : $input";
        return $this->kernel->generate($prompt);
    }
}
