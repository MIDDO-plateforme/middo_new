<?php

namespace App\AI\Validator;

use App\AI\Pipeline\AbstractPipeline;

final class PipelineValidator
{
    public function validate(AbstractPipeline $pipeline): void
    {
        // Pour l’instant on délègue à la validation interne
        $pipeline->validate();
        // Plus tard : règles supplémentaires (types de steps, cohérence, etc.)
    }
}
