<?php

namespace App\AI\Step;

use App\AI\Pipeline\PipelineContext;

final class ExtractTextStep implements StepInterface
{
    public function getName(): string
    {
        return 'extract_text';
    }

    public function execute(PipelineContext $context): StepResult
    {
        $input = $context->get('input');

        // Si c’est déjà du texte, on le renvoie tel quel
        if (is_string($input)) {
            return new StepResult('text', $input);
        }

        // Placeholder pour extraction PDF / image
        return new StepResult('text', '[EXTRACTION NON IMPLEMENTÉE]');
    }
}
