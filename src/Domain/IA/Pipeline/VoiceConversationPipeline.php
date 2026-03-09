<?php

namespace App\Domain\IA\Pipeline;

use App\Infrastructure\IA\Provider\SpeechInProvider;
use App\Infrastructure\IA\Provider\SpeechOutProvider;
use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;

class VoiceConversationPipeline implements IAPipelineEngineInterface
{
    public function __construct(
        private SpeechInProvider $speechIn,
        private ConversationTranslationPipeline $textPipeline,
        private SpeechOutProvider $speechOut
    ) {}

    /**
     * IARequest->prompt = audio (base64 ou autre)
     * IASettings->targetLanguage = langue cible (ex: 'fr', 'ar', 'pt')
     */
    public function run(IARequest $request): IAResponse
    {
        // 1) Audio source → texte source
        $sourceText = $this->speechIn->transcribe($request->prompt);

        // 2) Texte source → texte traduit
        $textResponse = $this->textPipeline->run(
            new IARequest(
                prompt: $sourceText,
                settings: $request->settings
            )
        );

        // 3) Texte traduit → audio cible
        $audio = $this->speechOut->synthesize(
            $textResponse->text,
            $request->settings->targetLanguage ?? 'fr'
        );

        // On renvoie l’audio (et on garde les tokens du pipeline texte)
        return new IAResponse(
            text: $audio,
            tokensUsed: $textResponse->tokensUsed
        );
    }
}
