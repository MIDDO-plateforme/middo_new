<?php

namespace App\IA\Agent;

use App\IA\AiKernel;

class LegalAgent implements IaAgentInterface
{
    public function __construct(private AiKernel $kernel) {}

    public function getName(): string
    {
        return 'legal';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['juridique', 'loi', 'contrat']);
    }

    public function process(string $task, string $input): string
    {
        $prompt = "Tu es un assistant juridique. Donne des informations générales (pas de conseils légaux). Sujet : $input";
        return $this->kernel->generate($prompt);
    }
}
