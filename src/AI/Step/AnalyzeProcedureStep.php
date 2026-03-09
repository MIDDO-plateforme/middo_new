<?php

namespace App\AI\Step;

use App\AI\Pipeline\PipelineContext;
use App\AI\DTO\AIRequest;
use App\AI\Provider\ProviderRouter;

final class AnalyzeProcedureStep implements StepInterface
{
    public function __construct(
        private ?ProviderRouter $router = null
    ) {}

    public function getName(): string
    {
        return 'procedure_analysis';
    }

    public function execute(PipelineContext $context): StepResult
    {
        $text = $context->get('text');

        // Si pas de router (exécution à blanc)
        if (!$this->router) {
            return new StepResult('analysis', '[ANALYSE NON IMPLEMENTÉE]');
        }

        $request = new AIRequest(
            providerName: 'openai',
            model: 'gpt-4o-mini',
            prompt: "Analyse ce document administratif et identifie la procédure :\n\n" . $text
        );

        $response = $this->router->route($request);

        return new StepResult('analysis', $response->getContent());
    }
}
