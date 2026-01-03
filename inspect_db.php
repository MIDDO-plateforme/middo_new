<?php
$dbPath = __DIR__ . '/var/data.db';

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connexion réussie\n\n";
    
    // Voir la structure de la table project
    echo "📋 STRUCTURE TABLE 'project' :\n";
    $columns = $pdo->query("PRAGMA table_info(project)")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "  - {$col['name']} ({$col['type']})\n";
    }
    
    // Voir les données BRUTES
    echo "\n📄 DONNÉES BRUTES (premier projet) :\n";
    $stmt = $pdo->query("SELECT * FROM project LIMIT 1");
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($project) {
        foreach ($project as $key => $value) {
            $preview = strlen($value) > 150 ? substr($value, 0, 150) . "..." : $value;
            echo "\n[$key]:\n$preview\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ ERREUR : " . $e->getMessage() . "\n";
}