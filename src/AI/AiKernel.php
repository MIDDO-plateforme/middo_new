<?php

namespace App\AI;

use App\AI\DTO\AIResponse;
use App\AI\Provider\AIProviderInterface;
use App\IA\Context\IAContext;
use App\IA\Registry\IARegistry;
use App\IA\History\HistoryManager;
use Symfony\Component\Security\Core\Security;

class AiKernel
{
    private IARegistry $registry;
    private AIProviderInterface $engine;
    private HistoryManager $history;
    private Security $security;

    public function __construct(
        AIProviderInterface $engine,
        IARegistry $registry,
        HistoryManager $history,
        Security $security
    ) {
        $this->engine = $engine;
        $this->registry = $registry;
        $this->history = $history;
        $this->security = $security;
    }

    private function getUserPreferences(): array
    {
        $user = $this->security->getUser();
        return $user ? $user->iaSettings() : [];
    }

    private function applyPreferencesToPrompt(string $prompt): string
    {
        $prefs = $this->getUserPreferences();

        $tone = $prefs['tone'] ?? 'neutral';
        $detail = $prefs['detail'] ?? 'normal';
        $language = $prefs['language'] ?? 'fr';

        return <<<TXT
Tu es MIDDO IA. Respecte strictement les préférences suivantes :
- Ton : {$tone}
- Niveau de détail : {$detail}
- Langue : {$language}

Utilisateur : {$prompt}
TXT;
    }

    public function ask(string $prompt, string $pipelineName): AIResponse
    {
        $prompt = $this->applyPreferencesToPrompt($prompt);

        $prefs = $this->getUserPreferences();
        $model = $prefs['model'] ?? $this->registry->getDefaultModel();

        $context = new IAContext($prompt, $pipelineName);

        $response = $this->engine->generate(
            $context->getPrompt(),
            $model
        );

        $this->logHistory($pipelineName, $response);

        return $response;
    }

    private function logHistory(string $pipelineName, AIResponse $response): void
    {
        $this->history->add([
            'pipeline' => $pipelineName,
            'provider' => $response->getProvider(),
            'model' => $response->getModel(),
            'tokens' => $response->getTokens(),
            'content' => $response->getContent(),
        ]);
    }
}
