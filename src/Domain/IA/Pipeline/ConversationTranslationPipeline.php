<?php

namespace App\Domain\IA\Pipeline;

use App\Domain\IA\Pipeline\Step\LanguageDetectionStep;
use App\Infrastructure\IA\Provider\TranslationProvider;
use App\Domain\IA\Pipeline\Step\PostProcessStep;
use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;

/**
 * ConversationTranslationPipeline :
 * pipeline dédié à la traduction automatique dans les conversations.
 */
class ConversationTranslationPipeline implements IAPipelineEngineInterface
{
    public function __construct(
        private LanguageDetectionStep $detect,
        private TranslationProvider $translator,
        private PostProcessStep $post
    ) {}

    public function run(IARequest $request): IAResponse
    {
        // 1) Détection de langue
        $detected = $this->detect->process($request);

        // 2) Traduction via TranslationProvider
        $translated = $this->translator->generate(
            new IARequest(
                prompt: $request->prompt,
                settings: $request->settings
            )
        );

        // 3) Nettoyage final
        return $this->post->process(
            $request,
            $translated
        );
    }
}
