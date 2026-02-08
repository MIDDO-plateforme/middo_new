<?php
// Test direct OpenAI API sans Symfony
$apiKey = 'REMPLACE_PAR_TA_CLE'; // ?? À REMPLACER

if (strlen($apiKey) < 50 || $apiKey === 'REMPLACE_PAR_TA_CLE') {
    die("? ERREUR: Remplace REMPLACE_PAR_TA_CLE par ta vraie clé OpenAI\n");
}

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
curl_close($ch);

echo "?? Code HTTP: $httpCode\n";

if ($httpCode === 200) {
    $result = json_decode($response, true);
    echo "? SUCCÈS !\n";
    echo "Réponse GPT-4: " . $result['choices'][0]['message']['content'] . "\n";
} else {
    echo "? ÉCHEC\n";
    echo "Réponse: $response\n";
}
