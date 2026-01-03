<?php

$dbPath = __DIR__ . '/var/data.db';

echo "=== DIAGNOSTIC BASE DE DONNÉES UTF-8 ===\n\n";
echo "Fichier : $dbPath\n";
echo "Taille : " . filesize($dbPath) . " octets\n\n";

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connexion SQLite réussie !\n\n";
    
    // Lister les tables
    echo "=== TABLES DANS LA BASE ===\n";
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        echo "  - $table\n";
    }
    
    echo "\n=== STRUCTURE TABLE 'user' ===\n";
    
    if (in_array('user', $tables)) {
        // Voir la structure de la table user
        $columns = $pdo->query("PRAGMA table_info(user)")->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Colonnes disponibles :\n";
        foreach ($columns as $col) {
            echo "  - {$col['name']} ({$col['type']})\n";
        }
        
        echo "\n=== CONTENU TABLE 'user' ===\n";
        
        // Récupérer TOUTES les colonnes
        $user = $pdo->query("SELECT * FROM user LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "Premier utilisateur :\n";
            foreach ($user as $key => $value) {
                $display = is_string($value) && strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value;
                echo "  $key: $display\n";
            }
            
            // Vérifier les accents dans toutes les valeurs texte
            echo "\n=== VÉRIFICATION ACCENTS ===\n";
            $hasAccentsInDb = false;
            
            foreach ($user as $key => $value) {
                if (is_string($value) && preg_match('/[éèêëàâäôöûüçîï]/u', $value)) {
                    echo "  ✅ Accents trouvés dans : $key = $value\n";
                    $hasAccentsInDb = true;
                }
            }
            
            if (!$hasAccentsInDb) {
                echo "  ⚠️  Aucun accent français détecté dans les données\n";
            }
        }
    }
    
    // Tester l'insertion avec accents
    echo "\n=== TEST INSERTION AVEC ACCENTS ===\n";
    
    $pdo->exec("DROP TABLE IF EXISTS test_utf8");
    $pdo->exec("CREATE TABLE test_utf8 (id INTEGER PRIMARY KEY, texte TEXT)");
    
    $testText = "François crée MIDDO en RDC : collaboration, entraide, réussite !";
    $stmt = $pdo->prepare("INSERT INTO test_utf8 (texte) VALUES (:texte)");
    $stmt->execute(['texte' => $testText]);
    
    echo "Texte inséré : $testText\n";
    
    // Relire le texte
    $result = $pdo->query("SELECT texte FROM test_utf8")->fetchColumn();
    echo "Texte lu : $result\n\n";
    
    if ($result === $testText) {
        echo "✅ UTF-8 fonctionne PARFAITEMENT dans SQLite !\n";
    } else {
        echo "❌ Problème d'encodage détecté\n";
    }
    
    // Nettoyer
    $pdo->exec("DROP TABLE test_utf8");
    
} catch (PDOException $e) {
    echo "❌ ERREUR : " . $e->getMessage() . "\n";
}

echo "\n=== FIN DU DIAGNOSTIC ===\n";
