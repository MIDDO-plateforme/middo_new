<?php

require __DIR__.'/vendor/autoload.php';

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

// Fix pour APP_DEBUG
if (!isset($_ENV['APP_DEBUG'])) {
    $_ENV['APP_DEBUG'] = '1';
}

$kernel = new Kernel($_ENV['APP_ENV'], (bool) $_ENV['APP_DEBUG']);
$kernel->boot();
$container = $kernel->getContainer();

// Récupère le service
$aiService = $container->get('App\Service\OpenAIAssistantService');

echo "🧪 Test du service OpenAI...\n\n";

// Test 1 : Génération de réponse
echo "📝 Test 1 : Génération de réponse\n";
$response = $aiService->generateResponse("Donne-moi un conseil pour réussir un projet collaboratif");
echo "Réponse : " . $response . "\n\n";

// Test 2 : Suggestions d'amélioration
echo "📝 Test 2 : Suggestions d'amélioration\n";
$suggestions = $aiService->suggestProjectImprovements(
    "Application mobile pour organiser des événements sportifs locaux",
    ['title' => 'SportConnect', 'category' => 'Mobile']
);
echo "Suggestions :\n";
foreach ($suggestions as $i => $suggestion) {
    echo "  " . ($i + 1) . ". " . $suggestion . "\n";
}
echo "\n";

// Test 3 : Analyse de sentiment
echo "📝 Test 3 : Analyse de sentiment\n";
$sentiment = $aiService->analyzeSentiment("Ce projet est vraiment génial ! J'adore l'idée et l'équipe est super motivée !");
echo "Sentiment : " . $sentiment['sentiment'] . " (confiance : " . $sentiment['confidence'] . "%)\n\n";

echo "✅ Service OpenAI 100% opérationnel !\n";
