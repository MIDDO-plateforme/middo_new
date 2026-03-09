<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeminiService
{
    private $httpClient;
    private $apiKey;
    private $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        // En production: récupérer depuis .env
        $this->apiKey = $_ENV['GEMINI_API_KEY'] ?? 'DEMO_MODE';
    }

    public function generateResponse(string $prompt, array $context = []): array
    {
        // Mode DEMO si pas de clé API
        if ($this->apiKey === 'DEMO_MODE') {
            return [
                'success' => true,
                'response' => $this->getDemoResponse($prompt),
                'mode' => 'demo',
            ];
        }

        try {
            $response = $this->httpClient->request('POST', $this->apiUrl . '?key=' . $this->apiKey, [
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $this->buildPrompt($prompt, $context)]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 1024,
                    ],
                ],
            ]);

            $data = $response->toArray();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Pas de réponse';

            return [
                'success' => true,
                'response' => $text,
                'mode' => 'gemini',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'response' => $this->getDemoResponse($prompt),
                'mode' => 'fallback',
            ];
        }
    }

    private function buildPrompt(string $userMessage, array $context): string
    {
        $systemPrompt = "Tu es l'assistant IA de MIDDO, une plateforme collaborative avec blockchain et ESCROW.\n\n";
        $systemPrompt .= "Contexte utilisateur:\n";
        $systemPrompt .= "- Profil: Développeur Symfony/React/Blockchain\n";
        $systemPrompt .= "- Compétences: PHP, JavaScript, Smart Contracts\n";
        $systemPrompt .= "- Wallet: 2,450€ disponible\n\n";
        $systemPrompt .= "Tu peux aider avec:\n";
        $systemPrompt .= "- Trouver des missions adaptées\n";
        $systemPrompt .= "- Gérer le wallet et les paiements\n";
        $systemPrompt .= "- Optimiser le profil\n";
        $systemPrompt .= "- Expliquer le système ESCROW\n\n";
        $systemPrompt .= "Réponds de manière concise et professionnelle.\n\n";
        $systemPrompt .= "Message utilisateur: " . $userMessage;

        return $systemPrompt;
    }

    private function getDemoResponse(string $message): string
    {
        $message = strtolower($message);

        if (strpos($message, 'mission') !== false || strpos($message, 'projet') !== false) {
            return "🎯 **Missions disponibles pour vous:**\n\n" .
                   "1. **Audit Smart Contract** - 1,200€ - DeFi Corp\n" .
                   "   • Durée: 2 semaines\n" .
                   "   • Match: 92%\n\n" .
                   "2. **Développement dApp** - 3,500€ - TechStart SAS\n" .
                   "    Durée: 2 mois\n" .
                   "    Match: 88%\n\n" .
                   "3. **Conseil Blockchain** - 800€/h - CryptoVentures\n" .
                   "    Durée: 1 mois\n" .
                   "    Match: 85%\n\n" .
                   "Souhaitez-vous postuler ou en savoir plus ?";
        }

        if (strpos($message, 'wallet') !== false || strpos($message, 'solde') !== false) {
            return " **Votre wallet MIDDO:**\n\n" .
                   " **Disponible:** 2,450€\n" .
                   " **En ESCROW:** 1,200€\n" .
                   " **À recevoir:** 3,500€ (livraison prévue 5 jan.)\n" .
                   " **Historique:** 8 transactions ce mois\n\n" .
                   "Tout est sécurisé via notre système ESCROW blockchain. Besoin d'un retrait ?";
        }

        if (strpos($message, 'profil') !== false) {
            return " **Analyse de votre profil:**\n\n" .
                   " **Forces:**\n" .
                   "• Taux de succès: 98% (Top Rated)\n" .
                   "• Note moyenne: 4.9/5 ⭐\n" .
                   "• 24 projets complétés\n\n" .
                   "⚠️ **À améliorer:**\n" .
                   "• Portfolio incomplet (95%)\n" .
                   "• Ajouter 2 certifications\n\n" .
                   "**Recommandations:** Ajoutez des captures d'écran de vos projets blockchain pour atteindre 100% !";
        }

        return " Je suis votre assistant IA MIDDO !\n\n" .
               "Je peux vous aider avec :\n" .
               "  Trouver des missions parfaites\n" .
               "  Gérer votre wallet\n" .
               "  Optimiser votre profil\n" .
               "  Analyser vos performances\n\n" .
               "**Mode:** DEMO (intégration Gemini Pro prévue)\n\n" .
               "Que souhaitez-vous faire ?";
    }
}