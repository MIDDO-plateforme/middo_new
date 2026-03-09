<?php

namespace App\Domain\IA\Pipeline\Step;

use App\Domain\IA\Pipeline\IAPipelineStepInterface;
use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;

/**
 * LanguageDetectionStep : détecte automatiquement la langue du texte.
 */
class LanguageDetectionStep implements IAPipelineStepInterface
{
    public function process(IARequest $request, ?IAResponse $previousResponse = null): IAResponse
    {
        $text = strtolower($request->prompt);

        // Détection simple (améliorable)
        if (preg_match('/[اأإء-ي]/u', $text)) $lang = 'ar';
        elseif (preg_match('/[а-яА-Я]/u', $text)) $lang = 'ru';
        elseif (preg_match('/[一-龯]/u', $text)) $lang = 'zh';
        elseif (preg_match('/[ぁ-ゔァ-ヴ]/u', $text)) $lang = 'ja';
        elseif (preg_match('/[가-힣]/u', $text)) $lang = 'ko';
        else $lang = 'auto';

        return new IAResponse(
            text: $lang,
            tokensUsed: 0
        );
    }
}
