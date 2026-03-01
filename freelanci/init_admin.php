<?php
require_once __DIR__ . '/config/database.php';

$pdo = getDBConnection();
$password = 'admin123';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = 'admin@freelanci.com'");
$stmt->execute();
$admin = $stmt->fetch();

if ($admin) {
    $stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE email = 'admin@freelanci.com'");
    if ($stmt->execute([$hashedPassword])) {
        echo "✅ Mot de passe administrateur mis à jour avec succès !\n";
        echo "Email: admin@freelanci.com\n";
        echo "Mot de passe: admin123\n";
    } else {
        echo "❌ Erreur lors de la mise à jour.\n";
    }
} else {
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, type_utilisateur) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute(['Admin', 'Freelanci', 'admin@freelanci.com', $hashedPassword, 'admin'])) {
        echo "✅ Compte administrateur créé avec succès !\n";
        echo "Email: admin@freelanci.com\n";
        echo "Mot de passe: admin123\n";
    } else {
        echo "❌ Erreur lors de la création.\n";
    }
}

echo "\n⚠️  IMPORTANT: Changez ce mot de passe après la première connexion !\n";
