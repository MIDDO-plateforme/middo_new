<?php

namespace App\IA\Agent;

use App\IA\Cortex\CortexState;

class CortexOrchestratorAgent implements IaAgentInterface
{
    public function __construct(
        private CortexState $cortex,
        private FluxPipelineAgent $fluxPipeline,
        private AdaptiveAgent $adaptive,
        private PredictiveAgent $predictive,
        private SimulationAgent $simulation,
        private GlobalStateAgent $globalState,
        private VisionAgent $vision
    ) {}

    public function getName(): string
    {
        return 'cortex-orchestrator';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['cortex', 'orchestration-cortex', 'os-cognitif']);
    }

    public function process(string $task, string $input): string
    {
        $fluxResult = $this->fluxPipeline->process('flux-pipeline', $input);
        $this->cortex->set('last_flux_pipeline', json_decode($fluxResult, true));

        $predictiveResult = $this->predictive->process('predictive', $fluxResult);
        $this->cortex->set('last_prediction', json_decode($predictiveResult, true));

        $simulationResult = $this->simulation->process('simulation', $fluxResult);
        $this->cortex->set('last_simulation', json_decode($simulationResult, true));

        $globalStateResult = $this->globalState->process('global-state', $input);
        $visionResult = $this->vision->process('vision', $input);

        $this->cortex->set('last_global_state', json_decode($globalStateResult, true));
        $this->cortex->set('last_vision', json_decode($visionResult, true));

        $result = [
            'flux_pipeline' => json_decode($fluxResult, true),
            'prediction' => json_decode($predictiveResult, true),
            'simulation' => json_decode($simulationResult, true),
            'etat_global' => json_decode($globalStateResult, true),
            'vision' => json_decode($visionResult, true),
            'cortex_snapshot' => $this->cortex->getAll(),
        ];

        return json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
