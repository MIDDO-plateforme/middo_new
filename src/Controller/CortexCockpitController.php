<?php

namespace App\Controller;

use App\IA\AiKernel;
use App\IA\Memory\MemoryStore;
use App\IA\Memory\VectorStoreInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CortexCockpitController extends AbstractController
{
    public function __construct(
        private AiKernel $kernel,
        private MemoryStore $memoryStore,
        private VectorStoreInterface $vectorStore
    ) {}

    #[Route('/api/cortex/flux', name: 'api_cortex_flux', methods: ['POST'])]
    public function flux(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $flux = $data['flux'] ?? '';

        $snapshot = $this->kernel->runTask('cortex', $flux);

        return new JsonResponse([
            'snapshot' => json_decode($snapshot, true),
        ]);
    }

    #[Route('/api/cortex/state', name: 'api_cortex_state', methods: ['GET'])]
    public function state(): JsonResponse
    {
        return new JsonResponse([
            'state' => $this->kernel->getGlobalState(),
        ]);
    }

    #[Route('/api/memory/all', name: 'api_memory_all', methods: ['GET'])]
    public function memoryAll(): JsonResponse
    {
        return new JsonResponse([
            'memory' => $this->memoryStore->all(),
        ]);
    }

    #[Route('/api/memory/search', name: 'api_memory_search', methods: ['GET'])]
    public function memorySearch(Request $request): JsonResponse
    {
        $q = $request->query->get('q', '');

        return new JsonResponse([
            'query' => $q,
            'results' => $this->memoryStore->search($q),
        ]);
    }

    #[Route('/api/vector/search', name: 'api_vector_search', methods: ['GET'])]
    public function vectorSearch(Request $request): JsonResponse
    {
        $q = $request->query->get('q', '');

        $embedding = $this->kernel->runTask('vector-memory-search', $q);

        return new JsonResponse([
            'query' => $q,
            'results' => json_decode($embedding, true),
        ]);
    }

    #[Route('/api/actions/plan', name: 'api_actions_plan', methods: ['POST'])]
    public function actionsPlan(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $input = $data['input'] ?? '';

        $plan = $this->kernel->runTask('action-planner', $input);

        return new JsonResponse([
            'plan' => json_decode($plan, true),
        ]);
    }

    #[Route('/api/actions/safety', name: 'api_actions_safety', methods: ['POST'])]
    public function actionsSafety(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $input = $data['input'] ?? '';

        $safety = $this->kernel->runTask('action-safety', $input);

        return new JsonResponse([
            'safety' => json_decode($safety, true),
        ]);
    }

    #[Route('/api/actions/execute', name: 'api_actions_execute', methods: ['POST'])]
    public function actionsExecute(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $input = $data['input'] ?? '';

        $execution = $this->kernel->runTask('action-executor', $input);

        return new JsonResponse([
            'execution' => json_decode($execution, true),
        ]);
    }
}
