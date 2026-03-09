<?php

namespace App\IA\Pipeline;

use App\IA\AiKernel;
use App\AI\DTO\AIResponse;

final class PaymentPipeline
{
    public function __construct(
        private readonly AiKernel $kernel
    ) {}

    public function analyzeInvoice(string $text, string $model = 'gpt-4o'): AIResponse
    {
        $prompt = <<<TXT
Analyse cette facture :

$text

Retourne :
- montant total
- taxes
- date
- fournisseur
- anomalies éventuelles
TXT;

        return $this->kernel->askBest($prompt, $model, 'analysis');
    }
}
