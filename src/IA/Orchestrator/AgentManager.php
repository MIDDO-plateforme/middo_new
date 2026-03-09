<?php

namespace App\IA\Orchestrator;

use App\IA\Agents\PlannerAgent;
use App\IA\Agents\SafetyAgent;
use App\IA\Agents\ExecutorAgent;
use App\IA\Exception\IaAgentException;
use Psr\Log\LoggerInterface;

class AgentManager
{
    private array $agentsState = [
        'planner' => ['active' => false, 'result' => null, 'timestamp' => null],
        'safety' => ['active' => false, 'result' => null, 'timestamp' => null],
        'executor' => ['active' => false, 'result' => null, 'timestamp' => null],
    ];

    public function __construct(
        private PlannerAgent $planner,
        private SafetyAgent $safety,
        private ExecutorAgent $executor,
        private LoggerInterface $logger,
    ) {
    }

    private function resetAgents(): void
    {
        foreach ($this->agentsState as &$agent) {
            $agent['active'] = false;
        }
    }

    public function runPlanner(string $context): array
    {
        $this->logger->info('AgentManager: runPlanner', ['context_preview' => mb_substr($context, 0, 120)]);
        $this->resetAgents();

        try {
            $result = $this->planner->plan($context);
        } catch (\Throwable $e) {
            $this->logger->error('PlannerAgent failed', ['error' => $e->getMessage()]);
            throw new IaAgentException('Erreur PlannerAgent', 0, $e);
        }

        $this->agentsState['planner'] = [
            'active' => true,
            'result' => $result,
            'timestamp' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        ];

        return $result;
    }

    public function runSafetyCheck(string $context): array
    {
        $this->logger->info('AgentManager: runSafetyCheck', ['context_preview' => mb_substr($context, 0, 120)]);
        $this->resetAgents();

        try {
            $result = $this->safety->check($context);
        } catch (\Throwable $e) {
            $this->logger->error('SafetyAgent failed', ['error' => $e->getMessage()]);
            throw new IaAgentException('Erreur SafetyAgent', 0, $e);
        }

        $this->agentsState['safety'] = [
            'active' => true,
            'result' => $result,
            'timestamp' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        ];

        return $result;
    }

    public function runExecutor(string $context): array
    {
        $this->logger->info('AgentManager: runExecutor', ['context_preview' => mb_substr($context, 0, 120)]);
        $this->resetAgents();

        try {
            $result = $this->executor->execute($context);
        } catch (\Throwable $e) {
            $this->logger->error('ExecutorAgent failed', ['error' => $e->getMessage()]);
            throw new IaAgentException('Erreur ExecutorAgent', 0, $e);
        }

        $this->agentsState['executor'] = [
            'active' => true,
            'result' => $result,
            'timestamp' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        ];

        return $result;
    }

    public function getAgentsState(): array
    {
        return $this->agentsState;
    }
}
