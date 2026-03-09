<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

// Charger .env
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__.'/.env');

$apiKey = $_ENV['OPENAI_API_KEY'];

echo "=== TEST IA AVEC ACCENTS FRANÇAIS ===\n";
echo "Clé OpenAI : " . substr($apiKey, 0, 20) . "...\n\n";

// Test avec accents français
$message = "Bonjour ! Je m'appelle François. J'ai créé une plateforme collaborative nommée MIDDO pour aider les entrepreneurs en RDC. Peux-tu me résumer cette idée en français avec des accents ?";

echo "Message envoyé :\n$message\n\n";

$ch = curl_init('https://api.openai.com/v1/chat/completions');

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json; charset=utf-8',
    ],
    CURLOPT_POSTFIELDS => json_encode([
        'model' => 'gpt-4o-mini',
        'messages' => [
            ['role' => 'user', 'content' => $message]
        ],
        'temperature' => 0.7,
        'max_tokens' => 200,
    ], JSON_UNESCAPED_UNICODE),
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Code HTTP : $httpCode\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    $reply = $data['choices'][0]['message']['content'] ?? 'Pas de réponse';
    
    echo "✅ SUCCÈS !\n\n";
    echo "Réponse de l'IA :\n";
    echo "─────────────────────────────────────\n";
    echo $reply . "\n";
    echo "─────────────────────────────────────\n\n";
    
    // Vérifier les accents
    $hasAccents = (
        strpos($reply, 'é') !== false || 
        strpos($reply, 'è') !== false || 
        strpos($reply, 'à') !== false ||
        strpos($reply, 'ê') !== false ||
        strpos($reply, 'ç') !== false
    );
    
    if ($hasAccents) {
        echo "✅ Les accents français sont BIEN AFFICHÉS !\n";
    } else {
        echo "⚠️  Pas d'accents détectés (peut-être normal selon la réponse)\n";
    }
    
} else {
    echo "❌ ERREUR HTTP $httpCode\n";
    echo $response . "\n";
}
