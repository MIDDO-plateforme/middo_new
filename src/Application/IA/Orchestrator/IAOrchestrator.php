<?php

namespace App\Application\IA\Orchestrator;

use App\Domain\IA\Pipeline\SessionPipeline;
use App\Domain\IA\Pipeline\RiskAnalysisPipeline;
use App\Domain\IA\Pipeline\DecisionSupportPipeline;
use App\Domain\IA\Pipeline\VoiceConversationPipeline;
use App\Domain\IA\Pipeline\ConversationTranslationPipeline;
use App\Domain\IA\Pipeline\VisionPipeline;
use App\Domain\IA\Pipeline\TaskGuidancePipeline;
use App\Domain\IA\Pipeline\AdminHelperPipeline;
use App\Domain\IA\Pipeline\PageConsistencyPipeline;
use App\Domain\IA\Pipeline\DocumentRouterPipeline;
use App\Application\IA\Command\GenericIACommand;
use App\Domain\IA\ValueObject\IAResponse;

class IAOrchestrator
{
    public function __construct(
        private SessionPipeline $session,
        private RiskAnalysisPipeline $risk,
        private DecisionSupportPipeline $decision,
        private VoiceConversationPipeline $voice,
        private ConversationTranslationPipeline $translate,
        private VisionPipeline $vision,
        private TaskGuidancePipeline $guide,
        private AdminHelperPipeline $adminHelper,
        private PageConsistencyPipeline $consistency,
        private DocumentRouterPipeline $documentRouter
    ) {}

    public function handle(GenericIACommand $command): IAResponse
    {
        $text = mb_strtolower($command->prompt);

        // AUDIO
        if ($this->isAudio($command->prompt)) {
            return $this->voice->run($command->toRequest());
        }

        // IMAGE (caméra / scan / upload)
        if ($this->isImage($command->prompt)) {

            // Analyse Vision (OCR + description)
            $visionResponse = $this->vision->run($command->toRequest());
            $visionText = mb_strtolower($visionResponse->text);

            // Si Vision détecte un document administratif → AdminHelper
            if ($this->isAdminDocument($visionText)) {

                $payload = json_encode([
                    'title' => 'Document administratif détecté',
                    'country' => null,
                    'description' => null,
                    'ocr' => $visionResponse->text
                ]);

                return $this->adminHelper->run(
                    $command->toRequest()->withPrompt($payload)
                );
            }

            return $visionResponse;
        }

        // DOCUMENT ROUTER (comptable, business, association, légal, etc.)
        if (
            str_contains($text, 'dossier complet') ||
            str_contains($text, 'document complet') ||
            str_contains($text, 'cabinet comptable') ||
            str_contains($text, 'classe les documents') ||
            str_contains($text, 'range les factures') ||
            str_contains($text, 'analyse le document') ||
            str_contains($text, 'router le document')
        ) {
            return $this->documentRouter->run($command->toRequest());
        }

        // ADMIN JSON (title + country)
        if ($this->isAdminJson($command->prompt)) {
            return $this->adminHelper->run($command->toRequest());
        }

        // ADMIN INTENT (texte)
        if ($this->isAdminIntent($text)) {
            return $this->adminHelper->run($command->toRequest());
        }

        // GUIDANCE (formulaire, aide étape par étape)
        if ($this->isGuidanceIntent($text)) {
            return $this->guide->run($command->toRequest());
        }

        // PAGE CONSISTENCY (vérification des pages)
        if (
            str_contains($text, 'cohérence') ||
            str_contains($text, 'pages') ||
            str_contains($text, 'vérifie les pages')
        ) {
            return $this->consistency->run($command->toRequest());
        }

        // RISK
        if (str_contains($text, 'risque') || str_contains($text, 'arnaque')) {
            return $this->risk->run($command->toRequest());
        }

        // DECISION
        if (str_contains($text, 'décide') || str_contains($text, 'choisir') || str_contains($text, 'option')) {
            return $this->decision->run($command->toRequest());
        }

        // TRANSLATION
        if (str_contains($text, 'traduis') || str_contains($text, 'traduction')) {
            return $this->translate->run($command->toRequest());
        }

        // DEFAULT → MEMORY PIPELINE
        return $this->session->run($command->toRequest());
    }

    private function isAudio(string $input): bool
    {
        return str_starts_with($input, 'data:audio');
    }

    private function isImage(string $input): bool
    {
        return str_starts_with($input, 'data:image');
    }

    private function isAdminJson(string $input): bool
    {
        $data = json_decode($input, true);
        return is_array($data) && isset($data['title']) && isset($data['country']);
    }

    private function isAdminIntent(string $text): bool
    {
        return str_contains($text, 'formulaire')
            || str_contains($text, 'démarche')
            || str_contains($text, 'allocations')
            || str_contains($text, 'aide au logement')
            || str_contains($text, 'administratif')
            || str_contains($text, 'dossier');
    }

    private function isGuidanceIntent(string $text): bool
    {
        return str_contains($text, 'aide-moi')
            || str_contains($text, 'aide moi')
            || str_contains($text, 'remplir')
            || str_contains($text, 'démarche')
            || str_contains($text, 'formulaire')
            || str_contains($text, 'guide-moi')
            || str_contains($text, 'guide moi');
    }

    private function isAdminDocument(string $text): bool
    {
        return str_contains($text, 'caf')
            || str_contains($text, 'cnps')
            || str_contains($text, 'inss')
            || str_contains($text, 'social security')
            || str_contains($text, 'formulaire')
            || str_contains($text, 'allocations')
            || str_contains($text, 'demande')
            || str_contains($text, 'dossier');
    }
}
