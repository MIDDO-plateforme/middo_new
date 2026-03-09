<?php

namespace App\Controller;

use App\AI\Pipeline\AdminHelperPipeline;
use App\AI\Compiler\PipelineCompiler;
use App\AI\Orchestrator\Orchestrator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class IAController extends AbstractController
{
    #[Route('/api/ia/admin-helper', name: 'api_ia_admin_helper', methods: ['POST'])]
    public function adminHelper(
        Request $request,
        AdminHelperPipeline $pipeline,
        PipelineCompiler $compiler,
        Orchestrator $orchestrator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['input'])) {
            return new JsonResponse([
                'error' => 'Missing "input" field.'
            ], 400);
        }

        // Compilation du pipeline
        $compiled = $compiler->compile($pipeline);

        // Exécution
        $context = $orchestrator->run($compiled, [
            'input' => $data['input']
        ]);

        return new JsonResponse([
            'pipeline' => $pipeline->getName(),
            'result' => $context->all()
        ]);
    }
}
