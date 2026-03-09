<?php

namespace App\Controller;

use App\IA\Exception\IaOrchestratorException;
use App\IA\Orchestrator\Orchestrator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ActionsController
{
    public function __construct(
        private Orchestrator $orchestrator,
    ) {
    }

    #[Route('/api/actions/plan', name: 'api_actions_plan', methods: ['GET'])]
    public function plan(): JsonResponse
    {
        try {
            $snapshot = $this->orchestrator->generatePlan();

            return new JsonResponse([
                'plan' => $snapshot['cortex_state']['internal']['plan'] ?? null,
            ]);
        } catch (IaOrchestratorException $e) {
            return new JsonResponse([
                'error' => [
                    'code' => 'ORCHESTRATOR_PLAN_ERROR',
                    'message' => $e->getMessage(),
                    'context' => $e->getContext(),
                ],
            ], 500);
        }
    }

    #[Route('/api/actions/safety', name: 'api_actions_safety', methods: ['GET'])]
    public function safety(): JsonResponse
    {
        try {
            $snapshot = $this->orchestrator->checkSafety();

            return new JsonResponse([
                'safety' => $snapshot['cortex_state']['internal']['safety'] ?? null,
            ]);
        } catch (IaOrchestratorException $e) {
            return new JsonResponse([
                'error' => [
                    'code' => 'ORCHESTRATOR_SAFETY_ERROR',
                    'message' => $e->getMessage(),
                    'context' => $e->getContext(),
                ],
            ], 500);
        }
    }

    #[Route('/api/actions/execute', name: 'api_actions_execute', methods: ['GET'])]
    public function execute(): JsonResponse
    {
        try {
            $snapshot = $this->orchestrator->executePlan();

            return new JsonResponse([
                'execution' => $snapshot['cortex_state']['internal']['execution'] ?? null,
            ]);
        } catch (IaOrchestratorException $e) {
            return new JsonResponse([
                'error' => [
                    'code' => 'ORCHESTRATOR_EXEC_ERROR',
                    'message' => $e->getMessage(),
                    'context' => $e->getContext(),
                ],
            ], 500);
        }
    }
}
