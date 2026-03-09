<?php

namespace App\Controller;

use App\IA\Exception\IaOrchestratorException;
use App\IA\Orchestrator\Orchestrator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CortexController
{
    public function __construct(
        private Orchestrator $orchestrator,
    ) {
    }

    #[Route('/api/cortex/flux', name: 'api_cortex_flux', methods: ['POST'])]
    public function flux(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $flux = $data['flux'] ?? '';

        try {
            $snapshot = $this->orchestrator->ingestFlux($flux);

            return new JsonResponse(['snapshot' => $snapshot]);
        } catch (IaOrchestratorException $e) {
            return new JsonResponse([
                'error' => [
                    'code' => 'ORCHESTRATOR_INGEST_ERROR',
                    'message' => $e->getMessage(),
                    'context' => $e->getContext(),
                ],
            ], 500);
        }
    }
}
