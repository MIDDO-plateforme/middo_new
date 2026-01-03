<?php
// test_api_ia.php - Test des APIs IA MIDDO
// Ce script teste les APIs IA en utilisant cURL

echo "=== TEST DES APIs IA MIDDO ===\n\n";

// Configuration
$baseUrl = 'http://localhost:8000';

// Fonction pour faire une requête POST avec cURL
function testPostAPI($url, $data = []) {
    $ch = curl_init($url);
    
    // Configuration cURL
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json; charset=utf-8',
        'Accept: application/json'
    ]);
    
    // Exécuter la requête
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

// Test 1 : Chatbot IA
echo "========================================\n";
echo "1️⃣ TEST CHATBOT IA\n";
echo "========================================\n";
echo "URL : $baseUrl/api/ai/chat\n";
echo "Message envoyé : 'Bonjour MIDDO !'\n\n";

$result1 = testPostAPI("$baseUrl/api/ai/chat", [
    'message' => 'Bonjour MIDDO ! Peux-tu te présenter en français ?'
]);

echo "📊 Statut HTTP : " . $result1['status'] . "\n";

if ($result1['error']) {
    echo "❌ Erreur cURL : " . $result1['error'] . "\n\n";
} else {
    echo "✅ Réponse reçue !\n";
    echo "Contenu (premiers 600 caractères) :\n";
    echo substr($result1['response'], 0, 600) . "\n\n";
}

// Test 2 : Analyse de sentiment
echo "========================================\n";
echo "2️⃣ TEST ANALYSE DE SENTIMENT\n";
echo "========================================\n";
echo "URL : $baseUrl/api/ai/analyze-sentiment\n";
echo "Texte : 'Je suis très content de MIDDO !'\n\n";

$result2 = testPostAPI("$baseUrl/api/ai/analyze-sentiment", [
    'text' => 'Je suis très content de travailler sur MIDDO ! C\'est génial et innovant !'
]);

echo "📊 Statut HTTP : " . $result2['status'] . "\n";

if ($result2['error']) {
    echo "❌ Erreur cURL : " . $result2['error'] . "\n\n";
} else {
    echo "✅ Réponse reçue !\n";
    echo "Contenu complet :\n";
    echo $result2['response'] . "\n\n";
}

// Test 3 : Enrichissement de profil
echo "========================================\n";
echo "3️⃣ TEST ENRICHISSEMENT PROFIL\n";
echo "========================================\n";
echo "URL : $baseUrl/api/ai/enrich-profile\n";
echo "Bio : 'Entrepreneur en RDC'\n\n";

$result3 = testPostAPI("$baseUrl/api/ai/enrich-profile", [
    'bio' => 'Entrepreneur passionné par l\'innovation technologique en Afrique',
    'skills' => ['Leadership', 'Innovation', 'Tech']
]);

echo "📊 Statut HTTP : " . $result3['status'] . "\n";

if ($result3['error']) {
    echo "❌ Erreur cURL : " . $result3['error'] . "\n\n";
} else {
    echo "✅ Réponse reçue !\n";
    echo "Contenu (premiers 600 caractères) :\n";
    echo substr($result3['response'], 0, 600) . "\n\n";
}

echo "========================================\n";
echo "=== FIN DES TESTS ===\n";
echo "========================================\n";
