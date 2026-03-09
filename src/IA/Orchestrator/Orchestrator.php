<?php

namespace App\IA\Orchestrator;

use App\IA\Cortex\CortexEngine;
use App\IA\Exception\IaOrchestratorException;
use Psr\Log\LoggerInterface;

class Orchestrator
{
    public function __construct(
        private PipelineManager $pipelineManager,
        private AgentManager $agentManager,
        private MemoryManager $memoryManager,
        private SnapshotBuilder $snapshotBuilder,
        private CortexEngine $cortexEngine,
        private LoggerInterface $logger,
    ) {
    }

    public function ingestFlux(string $flux): array
    {
        try {
            $this->logger->info('Orchestrator: ingestFlux called', ['flux_preview' => mb_substr($flux, 0, 120)]);

            $this->pipelineManager->activateStep('ingestion', ['flux' => $flux]);
            $this->memoryManager->storeFlux($flux);

            $cortexState = $this->cortexEngine->processFlux($flux);

            $snapshot = $this->snapshotBuilder->buildSnapshot(
                $this->pipelineManager,
                $this->agentManager,
                $this->memoryManager,
                $cortexState,
                $flux
            );

            $this->logger->info('Orchestrator: ingestFlux completed');

            return $snapshot;
        } catch (\Throwable $e) {
            $this->logger->error('Orchestrator: ingestFlux failed', [
                'error' => $e->getMessage(),
            ]);

            throw new IaOrchestratorException(
                'Erreur lors du traitement du flux par le Cortex.',
                ['stage' => 'ingestion'],
                0,
                $e
            );
        }
    }

    public function generatePlan(): array
    {
        try {
            $this->logger->info('Orchestrator: generatePlan called');

            $this->pipelineManager->activateStep('analyse');

            $context = $this->cortexEngine->getContextForPlanner();
            $plan = $this->agentManager->runPlanner($context);

            $this->memoryManager->storePlan($plan);

            $cortexState = $this->cortexEngine->updateState(['plan' => $plan]);

            $snapshot = $this->snapshotBuilder->buildSnapshot(
                $this->pipelineManager,
                $this->agentManager,
                $this->memoryManager,
                $cortexState
            );

            $this->logger->info('Orchestrator: generatePlan completed');

            return $snapshot;
        } catch (\Throwable $e) {
            $this->logger->error('Orchestrator: generatePlan failed', [
                'error' => $e->getMessage(),
            ]);

            throw new IaOrchestratorException(
                'Erreur lors de la génération du plan.',
                ['stage' => 'plan'],
                0,
                $e
            );
        }
    }

    public function checkSafety(): array
    {
        try {
            $this->logger->info('Orchestrator: checkSafety called');

            $this->pipelineManager->activateStep('supervision');

            $context = $this->cortexEngine->getContextForSafety();
            $safety = $this->agentManager->runSafetyCheck($context);

            $this->memoryManager->storeSafety($safety);

            $cortexState = $this->cortexEngine->updateState(['safety' => $safety]);

            $snapshot = $this->snapshotBuilder->buildSnapshot(
                $this->pipelineManager,
                $this->agentManager,
                $this->memoryManager,
                $cortexState
            );

            $this->logger->info('Orchestrator: checkSafety completed');

            return $snapshot;
        } catch (\Throwable $e) {
            $this->logger->error('Orchestrator: checkSafety failed', [
                'error' => $e->getMessage(),
            ]);

            throw new IaOrchestratorException(
                'Erreur lors de la vérification de sécurité.',
                ['stage' => 'safety'],
                0,
                $e
            );
        }
    }

    public function executePlan(): array
    {
        try {
            $this->logger->info('Orchestrator: executePlan called');

            $this->pipelineManager->activateStep('organisation');

            $context = $this->cortexEngine->getContextForExecutor();
            $execution = $this->agentManager->runExecutor($context);

            $this->memoryManager->storeExecution($execution);

            $cortexState = $this->cortexEngine->updateState(['execution' => $execution]);

            $snapshot = $this->snapshotBuilder->buildSnapshot(
                $this->pipelineManager,
                $this->agentManager,
                $this->memoryManager,
                $cortexState
            );

            $this->logger->info('Orchestrator: executePlan completed');

            return $snapshot;
        } catch (\Throwable $e) {
            $this->logger->error('Orchestrator: executePlan failed', [
                'error' => $e->getMessage(),
            ]);

            throw new IaOrchestratorException(
                "Erreur lors de l'exécution du plan.",
                ['stage' => 'execute'],
                0,
                $e
            );
        }
    }
}
