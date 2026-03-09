<?php

namespace App\Domain\IA\Pipeline;

use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;

interface IAPipelineEngineInterface
{
    /**
     * Exécute un pipeline complet sur une requête IA.
     */
    public function run(IARequest $request): IAResponse;
}
