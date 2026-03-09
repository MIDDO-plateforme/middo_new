<?php
// Test direct OpenAI + Anthropic
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "=== TEST DIRECT OpenAI ===\n";
$openaiKey = $_ENV['OPENAI_API_KEY'] ?? '';
echo "Cle OpenAI : " . substr($openaiKey, 0, 10) . "..." . substr($openaiKey, -10) . " (" . strlen($openaiKey) . " caracteres)\n";

if (!empty($openaiKey) && $openaiKey !== 'your_openai_key_here') {
    $data = [
        'model' => 'gpt-4',
        'messages' => [['role' => 'user', 'content' => 'Bonjour']],
        'max_tokens' => 50
    ];
    
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $openaiKey
            ],
            'content' => json_encode($data),
            'timeout' => 30
        ]
    ]);
    
    $response = @file_get_contents('https://api.openai.com/v1/chat/completions', false, $context);
    
    if ($response) {
        $result = json_decode($response, true);
        if (isset($result['choices'][0]['message']['content'])) {
            echo "OK OpenAI FONCTIONNE : " . $result['choices'][0]['message']['content'] . "\n";
        } else {
            echo "ERREUR reponse : " . json_encode($result) . "\n";
        }
    } else {
        $error = error_get_last();
        echo "ERREUR appel : " . ($error['message'] ?? 'Inconnue') . "\n";
    }
} else {
    echo "ERREUR Cle OpenAI manquante\n";
}

echo "\n=== TEST DIRECT Anthropic ===\n";
$anthropicKey = $_ENV['ANTHROPIC_API_KEY'] ?? '';
echo "Cle Anthropic : " . substr($anthropicKey, 0, 10) . "... (" . strlen($anthropicKey) . " caracteres)\n";

if (!empty($anthropicKey) && $anthropicKey !== 'your_anthropic_key_here') {
    if (strlen($anthropicKey) < 50) {
        echo "ATTENTION : Cle trop courte (" . strlen($anthropicKey) . " caracteres)\n";
        echo "   Une cle Anthropic valide fait ~100+ caracteres\n";
        echo "   Format attendu : sk-ant-api03-...\n";
    }
    
    $data = [
        'model' => 'claude-3-sonnet-20240229',
        'max_tokens' => 100,
        'messages' => [['role' => 'user', 'content' => 'Bonjour']]
    ];
    
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => [
                'Content-Type: application/json',
                'x-api-key: ' . $anthropicKey,
                'anthropic-version: 2023-06-01'
            ],
            'content' => json_encode($data),
            'timeout' => 30
        ]
    ]);
    
    $response = @file_get_contents('https://api.anthropic.com/v1/messages', false, $context);
    
    if ($response) {
        $result = json_decode($response, true);
        if (isset($result['content'][0]['text'])) {
            echo "OK Anthropic FONCTIONNE : " . $result['content'][0]['text'] . "\n";
        } else {
            echo "ERREUR reponse : " . json_encode($result) . "\n";
        }
    } else {
        $error = error_get_last();
        echo "ERREUR appel : " . ($error['message'] ?? 'Inconnue') . "\n";
    }
} else {
    echo "ERREUR Cle Anthropic manquante\n";
}

echo "\n=== FIN TESTS ===\n";