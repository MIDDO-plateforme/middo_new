# create-api-controllers.ps1
$baseDir = "C:\Users\MBANE LOKOTA\middo_new"
$apiDir = "$baseDir\src\Controller\Api"

Write-Host "🚀 CRÉATION AUTOMATIQUE APIs IA MIDDO" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green

if (-not (Test-Path $apiDir)) {
    New-Item -ItemType Directory -Path $apiDir -Force | Out-Null
}

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
    public function __construct(Client $openai) { $this->openai = $openai; }

    #[Route('/message', name: 'send_message', methods: ['POST'])]
    public function sendMessage(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $userMessage = $data['message'] ?? '';
            if (empty($userMessage)) {
                return $this->json(['success' => false, 'error' => 'Message vide'], 400);
            }
            $systemPrompt = "Tu es l'assistant IA de MIDDO, plateforme de collaboration professionnelle.";
            $response = $this->openai->chat()->create([
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userMessage]
                ],
                'max_tokens' => 500
            ]);
            return $this->json(['success' => true, 'message' => $response->choices[0]->message->content, 'timestamp' => time()]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => 'Erreur IA: ' . $e->getMessage()], 500);
        }
    }
}
'@

Set-Content -Path "$apiDir\ChatbotController.php" -Value $chatbotContent -Encoding UTF8
Write-Host "   ✅ ChatbotController créé" -ForegroundColor Green

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
    public function __construct(Client $openai) { $this->openai = $openai; }

    #[Route('/analyze', name: 'analyze', methods: ['POST'])]
    public function analyze(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $text = $data['text'] ?? '';
            if (empty($text)) { return $this->json(['success' => false, 'error' => 'Texte vide'], 400); }
            $response = $this->openai->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'Tu es expert en amélioration de contenu professionnel.'],
                    ['role' => 'user', 'content' => "Analyse et améliore: {$text}"]
                ],
                'max_tokens' => 400
            ]);
            $suggestions = array_filter(array_map('trim', explode("\n", $response->choices[0]->message->content)));
            return $this->json(['success' => true, 'suggestions' => $suggestions]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
'@

Set-Content -Path "$apiDir\SuggestionsController.php" -Value $suggestionsContent -Encoding UTF8
Write-Host "   ✅ SuggestionsController créé" -ForegroundColor Green
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
    public function __construct(Client $openai) { $this->openai = $openai; }

    #[Route('/find-profiles', name: 'find_profiles', methods: ['POST'])]
    public function findProfiles(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $projectDescription = $data['project_description'] ?? '';
            if (empty($projectDescription)) {
                return $this->json(['success' => false, 'error' => 'Description manquante'], 400);
            }
            $response = $this->openai->chat()->create([
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'system', 'content' => 'Tu es expert en recrutement.'],
                    ['role' => 'user', 'content' => "Trouve 5 profils pour: {$projectDescription}"]
                ],
                'max_tokens' => 800
            ]);
            $matches = json_decode($response->choices[0]->message->content, true) ?? [];
            return $this->json(['success' => true, 'matches' => $matches, 'total' => count($matches)]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
'@

Set-Content -Path "$apiDir\MatchingController.php" -Value $matchingContent -Encoding UTF8
Write-Host "   ✅ MatchingController créé" -ForegroundColor Green
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
    public function __construct(Client $openai) { $this->openai = $openai; }

    #[Route('/analyze', name: 'analyze', methods: ['POST'])]
    public function analyze(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $text = $data['text'] ?? '';
            if (empty($text)) { return $this->json(['success' => false, 'error' => 'Texte vide'], 400); }
            $response = $this->openai->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'Tu es expert en analyse de sentiment.'],
                    ['role' => 'user', 'content' => "Analyse sentiment: {$text}"]
                ],
                'max_tokens' => 300
            ]);
            $analysis = json_decode($response->choices[0]->message->content, true) ?? ['sentiment' => 'neutre'];
            return $this->json(['success' => true, 'analysis' => $analysis]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
'@

Set-Content -Path "$apiDir\SentimentController.php" -Value $sentimentContent -Encoding UTF8
Write-Host "   ✅ SentimentController créé" -ForegroundColor Green

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "✅ CRÉATION TERMINÉE !" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host "📁 Fichiers créés dans: $apiDir" -ForegroundColor Cyan
Write-Host "🔧 Prochaines étapes:" -ForegroundColor Yellow
Write-Host '   Remove-Item -Recurse -Force "C:\Users\MBANE LOKOTA\middo_new\var\cache\*"' -ForegroundColor Gray
Write-Host '   php -S localhost:8000 -t public' -ForegroundColor Gray
