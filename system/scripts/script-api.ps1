# create-api-controllers.ps1
# Script de création automatique des 4 contrôleurs API IA pour MIDDO

$baseDir = "C:\Users\MBANE LOKOTA\middo_new"
$apiDir = "$baseDir\src\Controller\Api"

Write-Host "🚀 CRÉATION AUTOMATIQUE APIs IA MIDDO" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

# Vérifier dossier Api existe
if (-not (Test-Path $apiDir)) {
    Write-Host "📁 Création dossier Api..." -ForegroundColor Yellow
    New-Item -ItemType Directory -Path $apiDir -Force | Out-Null
}

# ============================================
# CONTRÔLEUR 1/4 : ChatbotController
# ============================================
Write-Host "📝 1/4 - Création ChatbotController..." -ForegroundColor Cyan

$chatbotContent = @'
<?php

namespace App\Controller\Api;

use OpenAI\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/chatbot', name: 'api_chatbot_')]
class ChatbotController extends AbstractController
{
    private Client $openai;

    public function __construct(Client $openai)
    {
        $this->openai = $openai;
    }

    #[Route('/message', name: 'send_message', methods: ['POST'])]
    public function sendMessage(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $userMessage = $data['message'] ?? '';

            if (empty($userMessage)) {
                return $this->json([
                    'success' => false,
                    'error' => 'Message vide'
                ], 400);
            }

            // Contexte MIDDO pour améliorer réponses
            $systemPrompt = "Tu es l'assistant IA de MIDDO, plateforme de collaboration professionnelle. "
                . "Tu aides les utilisateurs à créer des projets, trouver des collaborateurs, "
                . "gérer leurs tâches et optimiser leur travail. Réponds de manière concise, "
                . "professionnelle et bienveillante en français.";

            $response = $this->openai->chat()->create([
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userMessage]
                ],
                'max_tokens' => 500,
                'temperature' => 0.7
            ]);

            $aiMessage = $response->choices[0]->message->content;

            return $this->json([
                'success' => true,
                'message' => $aiMessage,
                'timestamp' => time()
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Erreur IA: ' . $e->getMessage()
            ], 500);
        }
    }
}
'@

Set-Content -Path "$apiDir\ChatbotController.php" -Value $chatbotContent -Encoding UTF8
Write-Host "   ✅ ChatbotController créé" -ForegroundColor Green

# ============================================
# CONTRÔLEUR 2/4 : SuggestionsController
# ============================================
Write-Host "📝 2/4 - Création SuggestionsController..." -ForegroundColor Cyan

$suggestionsContent = @'
<?php

namespace App\Controller\Api;

use OpenAI\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/suggestions', name: 'api_suggestions_')]
class SuggestionsController extends AbstractController
{
    private Client $openai;

    public function __construct(Client $openai)
    {
        $this->openai = $openai;
    }

    #[Route('/analyze', name: 'analyze', methods: ['POST'])]
    public function analyze(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $text = $data['text'] ?? '';
            $type = $data['type'] ?? 'project'; // project, task, profile

            if (empty($text)) {
                return $this->json([
                    'success' => false,
                    'error' => 'Texte vide'
                ], 400);
            }

            $prompts = [
                'project' => "Analyse ce projet et fournis 3 suggestions concrètes pour l'améliorer: {$text}",
                'task' => "Analyse cette tâche et propose 3 optimisations: {$text}",
                'profile' => "Analyse ce profil professionnel et suggère 3 améliorations: {$text}"
            ];

            $response = $this->openai->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'Tu es expert en amélioration de contenu professionnel. Fournis des suggestions concrètes, numérotées et actionnables.'],
                    ['role' => 'user', 'content' => $prompts[$type] ?? $prompts['project']]
                ],
                'max_tokens' => 400,
                'temperature' => 0.8
            ]);

            $suggestions = $response->choices[0]->message->content;

            // Parser suggestions (séparer par lignes)
            $suggestionsList = array_filter(
                array_map('trim', explode("\n", $suggestions)),
                fn($line) => !empty($line)
            );

            return $this->json([
                'success' => true,
                'suggestions' => $suggestionsList,
                'type' => $type,
                'original_text' => $text
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Erreur suggestions: ' . $e->getMessage()
            ], 500);
        }
    }
}
'@

Set-Content -Path "$apiDir\SuggestionsController.php" -Value $suggestionsContent -Encoding UTF8
Write-Host "   ✅ SuggestionsController créé" -ForegroundColor Green

# ============================================
# CONTRÔLEUR 3/4 : MatchingController
# ============================================
Write-Host "📝 3/4 - Création MatchingController..." -ForegroundColor Cyan

$matchingContent = @'
<?php

namespace App\Controller\Api;

use OpenAI\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/matching', name: 'api_matching_')]
class MatchingController extends AbstractController
{
    private Client $openai;

    public function __construct(Client $openai)
    {
        $this->openai = $openai;
    }

    #[Route('/find-profiles', name: 'find_profiles', methods: ['POST'])]
    public function findProfiles(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $projectDescription = $data['project_description'] ?? '';
            $requiredSkills = $data['skills'] ?? [];

            if (empty($projectDescription)) {
                return $this->json([
                    'success' => false,
                    'error' => 'Description projet manquante'
                ], 400);
            }

            $skillsText = !empty($requiredSkills) 
                ? "Compétences requises: " . implode(', ', $requiredSkills)
                : '';

            $prompt = "Projet: {$projectDescription}\n\n{$skillsText}\n\n"
                . "Identifie les 5 types de profils professionnels les plus pertinents "
                . "pour ce projet. Pour chaque profil, indique:\n"
                . "1. Titre du profil\n"
                . "2. Compétences clés\n"
                . "3. Valeur ajoutée pour le projet\n\n"
                . "Format: JSON avec structure [{\"title\": \"\", \"skills\": [], \"value\": \"\"}]";

            $response = $this->openai->chat()->create([
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'system', 'content' => 'Tu es expert en recrutement et matching de talents. Analyse les besoins projet et recommande les profils idéaux.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 800,
                'temperature' => 0.6
            ]);

            $matchesText = $response->choices[0]->message->content;

            // Tenter parse JSON, sinon retourner texte brut
            $matches = json_decode($matchesText, true) ?? [
                ['title' => 'Profils suggérés', 'skills' => [], 'value' => $matchesText]
            ];

            return $this->json([
                'success' => true,
                'matches' => $matches,
                'total' => count($matches),
                'project_description' => $projectDescription
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Erreur matching: ' . $e->getMessage()
            ], 500);
        }
    }
}
'@

Set-Content -Path "$apiDir\MatchingController.php" -Value $matchingContent -Encoding UTF8
Write-Host "   ✅ MatchingController créé" -ForegroundColor Green

# ============================================
# CONTRÔLEUR 4/4 : SentimentController
# ============================================
Write-Host "📝 4/4 - Création SentimentController..." -ForegroundColor Cyan

$sentimentContent = @'
<?php

namespace App\Controller\Api;

use OpenAI\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/sentiment', name: 'api_sentiment_')]
class SentimentController extends AbstractController
{
    private Client $openai;

    public function __construct(Client $openai)
    {
        $this->openai = $openai;
    }

    #[Route('/analyze', name: 'analyze', methods: ['POST'])]
    public function analyze(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $text = $data['text'] ?? '';

            if (empty($text)) {
                return $this->json([
                    'success' => false,
                    'error' => 'Texte vide'
                ], 400);
            }

            $prompt = "Analyse le ton et sentiment de ce texte: \"{$text}\"\n\n"
                . "Fournis:\n"
                . "1. Sentiment global (positif/neutre/négatif)\n"
                . "2. Score de confiance (0-100)\n"
                . "3. Émotions détectées (max 3)\n"
                . "4. Ton du message (professionnel/amical/urgent/etc)\n"
                . "5. Conseil d'amélioration (si ton négatif/agressif)\n\n"
                . "Format JSON: {\"sentiment\": \"\", \"score\": 0, \"emotions\": [], \"tone\": \"\", \"advice\": \"\"}";

            $response = $this->openai->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'Tu es expert en analyse de sentiment et psychologie de la communication professionnelle.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 300,
                'temperature' => 0.3
            ]);

            $analysisText = $response->choices[0]->message->content;

            // Parse JSON ou structurer réponse
            $analysis = json_decode($analysisText, true) ?? [
                'sentiment' => 'neutre',
                'score' => 50,
                'emotions' => ['analyse en cours'],
                'tone' => 'indéterminé',
                'advice' => $analysisText
            ];

            return $this->json([
                'success' => true,
                'analysis' => $analysis,
                'original_text' => $text
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Erreur analyse sentiment: ' . $e->getMessage()
            ], 500);
        }
    }
}
'@

Set-Content -Path "$apiDir\SentimentController.php" -Value $sentimentContent -Encoding UTF8
Write-Host "   ✅ SentimentController créé" -ForegroundColor Green

# ============================================
# RÉCAPITULATIF
# ============================================
Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "✅ CRÉATION TERMINÉE AVEC SUCCÈS !" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "📁 Fichiers créés dans: $apiDir" -ForegroundColor Cyan
Write-Host ""
Write-Host "   1️⃣  ChatbotController.php" -ForegroundColor White
Write-Host "       Route: POST /api/chatbot/message" -ForegroundColor Gray
Write-Host ""
Write-Host "   2️⃣  SuggestionsController.php" -ForegroundColor White
Write-Host "       Route: POST /api/suggestions/analyze" -ForegroundColor Gray
Write-Host ""
Write-Host "   3️⃣  MatchingController.php" -ForegroundColor White
Write-Host "       Route: POST /api/ia_engine/matching/find-profiles" -ForegroundColor Gray
Write-Host ""
Write-Host "   4️⃣  SentimentController.php" -ForegroundColor White
Write-Host "       Route: POST /api/sentiment/analyze" -ForegroundColor Gray
Write-Host ""
Write-Host "🔧 PROCHAINES ÉTAPES:" -ForegroundColor Yellow
Write-Host "   1. Nettoyer cache Symfony" -ForegroundColor White
Write-Host "   2. Redémarrer serveur" -ForegroundColor White
Write-Host "   3. Tester APIs depuis interface" -ForegroundColor White
Write-Host ""
Write-Host "💡 Commandes à exécuter:" -ForegroundColor Cyan
Write-Host '   Remove-Item -Recurse -Force "C:\Users\MBANE LOKOTA\middo_new\var\cache\*"' -ForegroundColor Gray
Write-Host '   php -S localhost:8000 -t public' -ForegroundColor Gray
Write-Host ""
Write-Host "🎯 Test interface: http://localhost:8000/projects" -ForegroundColor Green
Write-Host ""