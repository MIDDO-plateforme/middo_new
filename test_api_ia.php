<?php
// test_api_ia.php - Test des APIs IA MIDDO
echo "=== TEST DES APIs IA MIDDO ===\n\n";

$baseUrl = 'http://localhost:8000';

function testPostAPI($url, $data = []) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json; charset=utf-8',
        'Accept: application/json'
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    return [
        'status' => $httpCode,
        'response' => $response,
        'error' => $error
    ];
}

echo "========================================\n";
echo "1️⃣ TEST CHATBOT IA\n";
echo "========================================\n";
echo "URL : $baseUrl/api/ai/chat\n\n";

$result1 = testPostAPI("$baseUrl/api/ai/chat", [
    'message' => 'Bonjour MIDDO ! Peux-tu te présenter en français ?'
]);

echo "📊 Statut HTTP : " . $result1['status'] . "\n";
if ($result1['error']) {
    echo "❌ Erreur : " . $result1['error'] . "\n\n";
} else {
    echo "✅ Réponse reçue !\n";
    echo substr($result1['response'], 0, 600) . "\n\n";
}

echo "========================================\n";
echo "2️⃣ TEST ANALYSE DE SENTIMENT\n";
echo "========================================\n";
echo "URL : $baseUrl/api/ai/analyze-sentiment\n\n";

$result2 = testPostAPI("$baseUrl/api/ai/analyze-sentiment", [
    'text' => 'Je suis très content de travailler sur MIDDO ! C\'est génial !'
]);

echo "📊 Statut HTTP : " . $result2['status'] . "\n";
if ($result2['error']) {
    echo "❌ Erreur : " . $result2['error'] . "\n\n";
} else {
    echo "✅ Réponse reçue !\n";
    echo $result2['response'] . "\n\n";
}

echo "========================================\n";
echo "3️⃣ TEST ENRICHISSEMENT PROFIL\n";
echo "========================================\n";
echo "URL : $baseUrl/api/ai/enrich-profile\n\n";

$result3 = testPostAPI("$baseUrl/api/ai/enrich-profile", [
    'bio' => 'Entrepreneur passionné par l\'innovation en Afrique',
    'skills' => ['Leadership', 'Innovation', 'Tech']
]);

echo "📊 Statut HTTP : " . $result3['status'] . "\n";
if ($result3['error']) {
    echo "❌ Erreur : " . $result3['error'] . "\n\n";
} else {
    echo "✅ Réponse reçue !\n";
    echo substr($result3['response'], 0, 600) . "\n\n";
}

echo "=== FIN DES TESTS ===\n";
