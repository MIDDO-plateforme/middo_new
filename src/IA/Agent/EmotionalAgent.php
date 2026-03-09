<?php

namespace App\IA\Agent;

use App\IA\AiKernel;

class EmotionalAgent implements IaAgentInterface
{
    public function __construct(private AiKernel $kernel) {}

    public function getName(): string
    {
        return 'emotional';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['émotion', 'soutien', 'écoute']);
    }

    public function process(string $task, string $input): string
    {
        $prompt = "Tu es un assistant empathique. Écoute, reformule, soutiens. Sujet : $input";
        return $this->kernel->generate($prompt);
    }
}
