<?php

// Lire le fichier .env directement
$envFile = __DIR__ . '/.env';
$apiKey = '';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, 'OPENAI_API_KEY=') === 0) {
            $apiKey = trim(substr($line, strlen('OPENAI_API_KEY=')));
            break;
        }
    }
}

echo "=== TEST CLÉ OPENAI ===\n";
echo "Clé présente : " . (!empty($apiKey) ? 'OUI' : 'NON') . "\n";
echo "Longueur : " . strlen($apiKey) . " caractères\n";
echo "Début : " . substr($apiKey, 0, 30) . "...\n";
echo "Fin : ..." . substr($apiKey, -30) . "\n";

if (!empty($apiKey)) {
    echo "\n=== TEST APPEL API OPENAI ===\n";
    
    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'model' => 'gpt-4o-mini',
        'messages' => [
            ['role' => 'user', 'content' => 'Réponds juste "OK"']
        ],
        'max_tokens' => 10
    ]));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    echo "Code HTTP : $httpCode\n";
    
    if (!empty($curlError)) {
        echo "❌ Erreur CURL : $curlError\n";
    } elseif ($httpCode === 200) {
        $data = json_decode($response, true);
        echo "✅ SUCCÈS ! Réponse : " . ($data['choices'][0]['message']['content'] ?? 'N/A') . "\n";
    } else {
        echo "❌ ERREUR HTTP $httpCode :\n";
        echo $response . "\n";
    }
} else {
    echo "\n❌ Clé API manquante ou vide !\n";
}