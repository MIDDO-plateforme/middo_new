<?php
// Activation du mode debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Chargement de l'environnement Symfony
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

echo "=== TEST DEBUG CONTROLLERS ===\n\n";

// TEST 1 - MatchingController
echo "1️⃣ TEST MatchingController...\n";
try {
    require_once __DIR__ . '/src/Controller/Api/MatchingController.php';
    echo "✅ MatchingController chargé sans erreur\n";
} catch (Exception $e) {
    echo "❌ ERREUR MatchingController: " . $e->getMessage() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}

// TEST 2 - SentimentController
echo "\n2️⃣ TEST SentimentController...\n";
try {
    require_once __DIR__ . '/src/Controller/Api/SentimentController.php';
    echo "✅ SentimentController chargé sans erreur\n";
} catch (Exception $e) {
    echo "❌ ERREUR SentimentController: " . $e->getMessage() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}

// TEST 3 - ChatbotController
echo "\n3️⃣ TEST ChatbotController...\n";
try {
    require_once __DIR__ . '/src/Controller/Api/ChatbotController.php';
    echo "✅ ChatbotController chargé sans erreur\n";
} catch (Exception $e) {
    echo "❌ ERREUR ChatbotController: " . $e->getMessage() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}

echo "\n=== FIN TEST ===\n";
