<?php
require_once __DIR__ . '/config/database.php';

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Test BDD Freelanci</title>";
echo "<style>body{font-family:sans-serif;padding:20px;max-width:600px;margin:0 auto;}";
echo ".ok{color:green;}.err{color:red;}.info{background:#e7f3ff;padding:15px;border-radius:8px;margin:10px 0;}</style></head><body>";
echo "<h1>🔍 Vérification base de données Freelanci</h1>";

try {
    $pdo = getDBConnection();
    echo "<p class='ok'>✓ Connexion à MySQL réussie</p>";
    $stmt = $pdo->query("SELECT DATABASE() as db");
    $row = $stmt->fetch();
    echo "<p class='ok'>✓ Base active : <strong>" . htmlspecialchars($row['db']) . "</strong></p>";
    $tables = ['utilisateurs', 'categories', 'services', 'commandes'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) as n FROM `$table`");
        $count = $stmt->fetch()['n'];
        echo "<p class='ok'>✓ Table <code>$table</code> : $count enregistrement(s)</p>";
    }
    
    echo "<div class='info'><strong>La base Freelanci est correctement connectée.</strong></div>";
    echo "<p><a href='index.php'>→ Retour à l'accueil</a></p>";
    
} catch (PDOException $e) {
    echo "<p class='err'>✗ Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<div class='info'>Vérifiez que :<br>1. XAMPP/MySQL est démarré<br>2. La base 'freelanci' existe (exécutez database.sql dans phpMyAdmin)</div>";
}

echo "</body></html>";
