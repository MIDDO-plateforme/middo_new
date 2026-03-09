<?php

namespace App\IA\Agent;

class AgentRouter
{
    /** @var IaAgentInterface[] */
    private array $agents;

    public function __construct(iterable $agents)
    {
        $this->agents = is_array($agents) ? $agents : iterator_to_array($agents);
    }

    public function route(string $task, string $input): string
    {
        foreach ($this->agents as $agent) {
            if ($agent->supports($task)) {
                return $agent->process($task, $input);
            }
        }

        throw new \RuntimeException("Aucun agent ne supporte la tâche : $task");
    }
}
