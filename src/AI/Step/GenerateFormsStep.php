<?php

namespace App\AI\Step;

use App\AI\Pipeline\PipelineContext;
use App\AI\DTO\AIRequest;
use App\AI\Provider\ProviderRouter;

final class GenerateFormsStep implements StepInterface
{
    public function __construct(
        private ?ProviderRouter $router = null
    ) {}

    public function getName(): string
    {
        return 'forms';
    }

    public function execute(PipelineContext $context): StepResult
    {
        $analysis = $context->get('analysis');

        if (!$this->router) {
            return new StepResult('forms', '[FORMULAIRES NON IMPLEMENTÉS]');
        }

        $request = new AIRequest(
            providerName: 'openai',
            model: 'gpt-4o-mini',
            prompt: "À partir de cette analyse, génère les formulaires nécessaires (structure JSON) :\n\n" . $analysis
        );

        $response = $this->router->route($request);

        return new StepResult('forms', $response->getContent());
    }
}
