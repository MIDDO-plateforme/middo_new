<?php

namespace App\Domain\IA\Pipeline;

use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;
use App\Infrastructure\IA\Provider\StrategyProvider;
use App\Infrastructure\IA\Provider\InsightProvider;

class DecisionSupportPipeline implements IAPipelineEngineInterface
{
    public function __construct(
        private StrategyProvider $strategy,
        private InsightProvider $insight
    ) {}

    public function run(IARequest $request): IAResponse
    {
        $text = $request->prompt;

        $strategy = $this->strategy->analyze($text);
        $insights = $this->insight->analyze($text);

        $out  = "Analyse de la situation :\n\n";
        $out .= "Contexte :\n" . $strategy['context'] . "\n\n";

        $out .= "Objectifs possibles :\n";
        foreach ($strategy['goals'] as $g) {
            $out .= "- {$g}\n";
        }

        $out .= "\nOptions d’action :\n";
        foreach ($strategy['options'] as $o) {
            $out .= "- {$o}\n";
        }

        $out .= "\nPoints de vigilance / angles à considérer :\n";
        foreach ($insights as $i) {
            $out .= "- {$i}\n";
        }

        return new IAResponse(
            text: $out,
            tokensUsed: 0
        );
    }
}
