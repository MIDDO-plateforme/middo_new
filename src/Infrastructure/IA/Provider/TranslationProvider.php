<?php

namespace App\Infrastructure\IA\Provider;

use App\Domain\IA\Service\IAProviderInterface;
use App\Domain\IA\Service\TokenCounterInterface;
use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;

/**
 * TranslationProvider : orchestrateur de traduction universelle.
 * Il choisit automatiquement le meilleur moteur selon les langues.
 */
class TranslationProvider implements IAProviderInterface
{
    public function __construct(
        private array $engines, // ['deepl' => DeepLProvider, 'nllb' => NLLBProvider, ...]
        private TokenCounterInterface $counter
    ) {}

    public function generate(IARequest $request): IAResponse
    {
        $text = $request->prompt;

        // Détection simple de langue (améliorable)
        $sourceLang = $this->detectLanguage($text);
        $targetLang = $request->settings->targetLanguage ?? 'fr';

        // Choix du moteur optimal
        $engine = $this->chooseEngine($sourceLang, $targetLang);

        /** @var IAProviderInterface $provider */
        $provider = $this->engines[$engine];

        // On construit une requête IA dédiée à la traduction
        $translationPrompt = "Traduire du {$sourceLang} vers {$targetLang} :\n\n" . $text;

        $translationRequest = new IARequest(
            prompt: $translationPrompt,
            settings: $request->settings
        );

        $response = $provider->generate($translationRequest);

        // Comptage des tokens
        $promptTokens = $this->counter->count($translationPrompt);
        $responseTokens = $this->counter->count($response->text);
        $totalTokens = $promptTokens + $responseTokens;

        return new IAResponse(
            text: $response->text,
            tokensUsed: $totalTokens
        );
    }

    private function detectLanguage(string $text): string
    {
        // Détection simple (placeholder)
        if (preg_match('/[اأإء-ي]/u', $text)) return 'ar';
        if (preg_match('/[а-яА-Я]/u', $text)) return 'ru';
        if (preg_match('/[一-龯]/u', $text)) return 'zh';
        if (preg_match('/[ぁ-ゔァ-ヴ]/u', $text)) return 'ja';
        if (preg_match('/[가-힣]/u', $text)) return 'ko';

        return 'auto';
    }

    private function chooseEngine(string $source, string $target): string
    {
        // Règles simples (améliorables)
        if ($source === 'auto') return 'deepl';
        if ($source === 'zh' || $target === 'zh') return 'nllb';
        if ($source === 'ar' || $target === 'ar') return 'nllb';

        return 'deepl';
    }
}
