<?php

namespace App\Domain\IA\Pipeline;

use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;
use App\Infrastructure\IA\Provider\FraudDetectionProvider;
use App\Infrastructure\IA\Provider\ConsistencyCheckProvider;

class RiskAnalysisPipeline implements IAPipelineEngineInterface
{
    public function __construct(
        private FraudDetectionProvider $fraud,
        private ConsistencyCheckProvider $consistency
    ) {}

    public function run(IARequest $request): IAResponse
    {
        $text = $request->prompt;

        $fraudResult = $this->fraud->analyze($text);
        $consistencyResult = $this->consistency->analyze($text);

        $risk = $fraudResult['risk'];
        $signals = $fraudResult['signals'];
        $consistency = $consistencyResult['consistency'];
        $notes = $consistencyResult['notes'];

        $summary = "Analyse de risque :\n";
        $summary .= "- Score de risque : " . round($risk * 100) . "%\n";
        $summary .= "- Cohérence estimée : " . round($consistency * 100) . "%\n";

        if (!empty($signals)) {
            $summary .= "\nSignaux détectés :\n";
            foreach ($signals as $s) {
                $summary .= "- {$s}\n";
            }
        }

        if (!empty($notes)) {
            $summary .= "\nNotes :\n";
            foreach ($notes as $n) {
                $summary .= "- {$n}\n";
            }
        }

        return new IAResponse(
            text: $summary,
            tokensUsed: 0
        );
    }
}
