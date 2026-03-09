<?php

namespace App\KernelPipelines;

class AdminHelperPipeline
{
    public static function build(): Pipeline
    {
        return new Pipeline([
            new Steps\CleanTextStep(),
            new Steps\DetectDocumentTypeStep(),
            new Steps\ExtractImportantDataStep(),
            new Steps\SummarizeStep(),
            new Steps\TranslateStep('fr'),
        ]);
    }
}
