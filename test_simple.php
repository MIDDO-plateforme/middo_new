<?php
// Test ultra-simple sans Dotenv

// Lire .env manuellement
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

echo "=== TEST OpenAI ===\n";
$openaiKey = $_ENV['OPENAI_API_KEY'] ?? '';
echo "Cle : " . substr($openaiKey, 0, 15) . "..." . substr($openaiKey, -10) . " (" . strlen($openaiKey) . " car.)\n";

if (strlen($openaiKey) > 20) {
    $data = json_encode([
        'model' => 'gpt-4',
        'messages' => [['role' => 'user', 'content' => 'Dis juste Bonjour']],
        'max_tokens' => 20
    ]);
    
    $opts = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\nAuthorization: Bearer " . $openaiKey,
            'content' => $data,
            'timeout' => 30
        ]
    ];
    
    $context = stream_context_create($opts);
    $response = @file_get_contents('https://api.openai.com/v1/chat/completions', false, $context);
    
    if ($response) {
        $result = json_decode($response, true);
        if (isset($result['choices'][0]['message']['content'])) {
            echo "✅ OpenAI OK : " . $result['choices'][0]['message']['content'] . "\n";
        } else {
            echo "❌ Reponse invalide : " . substr(json_encode($result), 0, 200) . "\n";
        }
    } else {
        echo "❌ Erreur appel OpenAI\n";
        if (isset($http_response_header)) {
            echo "Headers : " . implode(', ', $http_response_header) . "\n";
        }
    }
} else {
    echo "❌ Cle OpenAI manquante ou invalide\n";
}

echo "\n=== TEST Anthropic ===\n";
$anthropicKey = $_ENV['ANTHROPIC_API_KEY'] ?? '';
echo "Cle : " . substr($anthropicKey, 0, 15) . "... (" . strlen($anthropicKey) . " car.)\n";

if (strlen($anthropicKey) < 50) {
    echo "⚠️  Cle trop courte ! Attendu : ~100+ caracteres (format sk-ant-api03-...)\n";
}

if (strlen($anthropicKey) > 20) {
    $data = json_encode([
        'model' => 'claude-3-sonnet-20240229',
        'max_tokens' => 50,
        'messages' => [['role' => 'user', 'content' => 'Dis juste Bonjour']]
    ]);
    
    $opts = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\nx-api-key: " . $anthropicKey . "\r\nanthropic-version: 2023-06-01",
            'content' => $data,
            'timeout' => 30
        ]
    ];
    
    $context = stream_context_create($opts);
    $response = @file_get_contents('https://api.anthropic.com/v1/messages', false, $context);
    
    if ($response) {
        $result = json_decode($response, true);
        if (isset($result['content'][0]['text'])) {
            echo "✅ Anthropic OK : " . $result['content'][0]['text'] . "\n";
        } else {
            echo "❌ Reponse invalide : " . substr(json_encode($result), 0, 200) . "\n";
        }
    } else {
        echo "❌ Erreur appel Anthropic\n";
        if (isset($http_response_header)) {
            echo "Headers : " . implode(', ', $http_response_header) . "\n";
        }
    }
} else {
    echo "❌ Cle Anthropic manquante ou invalide\n";
}

echo "\n=== FIN ===\n";