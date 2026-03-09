<?php

namespace App\Application\Document;

use App\IA\AiKernel;

class DocumentAnalysisService
{
    private AiKernel $ai;
    private DocumentStorageService $storage;

    public function __construct(AiKernel $ai, DocumentStorageService $storage)
    {
        $this->ai = $ai;
        $this->storage = $storage;
    }

    public function analyze(string $filename): string
    {
        $path = $this->storage->getPath($filename);

        if (!file_exists($path)) {
            throw new \RuntimeException("Document introuvable.");
        }

        $content = file_get_contents($path);

        $prompt = <<<TXT
Analyse le document suivant et fournis :
- un résumé clair
- les points importants
- les risques éventuels
- les actions recommandées

Contenu du document :
{$content}
TXT;

        $response = $this->ai->ask($prompt, 'document_analysis');

        return $response->getContent();
    }
}
