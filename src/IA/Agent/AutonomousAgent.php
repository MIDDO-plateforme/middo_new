<?php

namespace App\IA\Agent;

use App\IA\AiKernel;

class AutonomousAgent implements IaAgentInterface
{
    public function __construct(
        private AiKernel $kernel,
        private PlanningAgent $planner,
        private WorkflowAgent $workflow
    ) {}

    public function getName(): string
    {
        return 'autonomous';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['auto', 'autonome', 'objectif-auto']);
    }

    public function process(string $task, string $input): string
    {
        $plan = $this->planner->process('planning', $input);
        return $this->workflow->process('workflow', $plan);
    }
}
