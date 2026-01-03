<?php

$data = json_encode([
    'message' => 'test',
    'expertise' => 'fullstack',
    'context' => 'test'
]);

$opts = [
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => $data,
        'timeout' => 30
    ]
];

$context = stream_context_create($opts);

echo "=== TEST APPEL FASTAPI /chat ===\n\n";

try {
    $response = file_get_contents('http://localhost:8000/chat', false, $context);
    echo "SUCCESS!\n";
    echo "Response: " . $response . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
