<?php

namespace App\Controller\IA;

use App\IA\Autonomous\AutonomousLoop;
use App\IA\Autonomous\AutonomousScheduler;
use App\IA\Autonomous\AutonomousState;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AutonomousTestController extends AbstractController
{
    #[Route('/api/ia/autonomous/start', name: 'api_ia_autonomous_start', methods: ['POST'])]
    public function start(
        AutonomousLoop $loop,
        AutonomousScheduler $scheduler,
        AutonomousState $state
    ): JsonResponse {
        $task = $scheduler->nextTask();
        $newState = $loop->run($state, $task);

        return $this->json([
            'status' => 'iteration_done',
            'autonomous_state' => [
                'status' => $newState->status,
                'currentTask' => $newState->currentTask,
                'iteration' => $newState->iteration,
            ],
        ]);
    }

    #[Route('/api/ia/autonomous/status', name: 'api_ia_autonomous_status', methods: ['GET'])]
    public function status(AutonomousState $state): JsonResponse
    {
        return $this->json([
            'autonomous_state' => [
                'status' => $state->status,
                'currentTask' => $state->currentTask,
                'iteration' => $state->iteration,
            ],
        ]);
    }
}
