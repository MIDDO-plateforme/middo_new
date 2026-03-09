<?php
echo "🔧 CORRECTION ENCODAGE BASE DE DONNÉES\n";
echo str_repeat("=", 60) . "\n\n";

$dbPath = __DIR__ . '/var/data.db';

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connexion à la base réussie\n\n";
    
    // Afficher l'état AVANT correction
    echo "📄 AVANT correction :\n";
    $stmt = $pdo->query("SELECT id, name, description FROM project LIMIT 3");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        echo "  ID {$row['id']}: {$row['name']}\n";
    }
    
    echo "\n🔄 Correction en cours...\n";
    
    // Corrections encodage
    $replacements = [
        'Ã©' => 'é',
        'Ã¨' => 'è',
        'Ãª' => 'ê',
        'Ã ' => 'à',
        'Ã§' => 'ç',
        'Ã´' => 'ô',
        'Ã»' => 'û',
        'DÃ©' => 'Dé',
        'AmÃ©' => 'Amé',
        'CrÃ©' => 'Cré',
        'PropriÃ©' => 'Proprié',
        'CatÃ©' => 'Caté',
    ];
    
    $count = 0;
    foreach ($replacements as $bad => $good) {
        $stmt = $pdo->prepare("UPDATE project SET name = REPLACE(name, :bad, :good)");
        $stmt->execute([':bad' => $bad, ':good' => $good]);
        $count += $stmt->rowCount();
        
        $stmt = $pdo->prepare("UPDATE project SET description = REPLACE(description, :bad, :good)");
        $stmt->execute([':bad' => $bad, ':good' => $good]);
        $count += $stmt->rowCount();
    }
    
    echo "✅ $count modification(s) effectuée(s)\n\n";
    
    // Afficher l'état APRÈS correction
    echo "📄 APRÈS correction :\n";
    $stmt = $pdo->query("SELECT id, name, description FROM project LIMIT 3");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        echo "  ID {$row['id']}: {$row['name']}\n";
    }
    
    echo "\n🎉 TERMINÉ ! Rechargez la page.\n";
    
} catch (Exception $e) {
    echo "❌ ERREUR : " . $e->getMessage() . "\n";
}