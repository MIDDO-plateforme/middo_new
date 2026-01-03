<?php
// Test direct OpenAI API sans Symfony
$apiKey = 'sk-proj-5MqvYxS0xWPTL3_JgS1l8PfxKqG5a_ZMgVBxA1v56gKF8b0XNy3u-c19WxqHFqYJ1o9ygz4A';

echo "?? Clé OpenAI: " . substr($apiKey, 0, 10) . "..." . substr($apiKey, -5) . " (" . strlen($apiKey) . " chars)\n\n";

$url = 'https://api.openai.com/v1/chat/completions';
$data = [
    'model' => 'gpt-4',
    'messages' => [
        ['role' => 'user', 'content' => 'Dis bonjour en une phrase']
    ],
    'max_tokens' => 50
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);

echo "?? Appel à OpenAI API...\n";
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "?? Code HTTP: $httpCode\n";

if ($curlError) {
    echo "? ERREUR CURL: $curlError\n";
    exit(1);
}

if ($httpCode === 200) {
    $result = json_decode($response, true);
    echo "? SUCCÈS !\n";
    echo "Réponse GPT-4: " . $result['choices'][0]['message']['content'] . "\n";
    echo "\n?? Utilisation:\n";
    echo "   Tokens: " . $result['usage']['total_tokens'] . "\n";
} else {
    echo "? ÉCHEC\n";
    echo "Réponse brute: $response\n";
    
    $errorData = json_decode($response, true);
    if (isset($errorData['error'])) {
        echo "\n?? Détails erreur:\n";
        echo "   Code: " . ($errorData['error']['code'] ?? 'N/A') . "\n";
        echo "   Message: " . ($errorData['error']['message'] ?? 'N/A') . "\n";
    }
}
