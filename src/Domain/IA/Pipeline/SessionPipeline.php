<?php

namespace App\Domain\IA\Pipeline;

use App\Application\IA\Service\SessionContextManager;
use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;

/**
 * SessionPipeline :
 * pipeline IA avec mémoire de session.
 */
class SessionPipeline implements IAPipelineEngineInterface
{
    public function __construct(
        private IAPipelineEngineInterface $corePipeline,
        private SessionContextManager $contextManager
    ) {}

    public function run(IARequest $request): IAResponse
    {
        $sessionId = $request->settings->sessionId ?? 'default';

        // 1) Charger le contexte (si tu veux l’utiliser dans le prompt plus tard)
        $context = $this->contextManager->loadContext($sessionId);

        // (Optionnel) : enrichir le prompt avec le contexte
        // Pour l’instant, on laisse simple : on passe le request tel quel au corePipeline

        $response = $this->corePipeline->run($request);

        // 2) Sauvegarder ce tour de conversation
        $this->contextManager->saveTurn($sessionId, $request, $response);

        return $response;
    }
}
