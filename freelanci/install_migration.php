<?php
require_once __DIR__ . '/config/database.php';

header('Content-Type: text/html; charset=utf-8');
echo "<pre>\n";

$pdo = getDBConnection();

function columnExists($pdo, $table, $col) {
    $stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE '$col'");
    return $stmt->rowCount() > 0;
}

function tableExists($pdo, $table) {
    $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
    return $stmt->rowCount() > 0;
}

try {
    if (!columnExists($pdo, 'utilisateurs', 'gouvernorat')) {
        $pdo->exec("ALTER TABLE utilisateurs ADD gouvernorat VARCHAR(80) NULL");
        echo "OK: utilisateurs.gouvernorat\n";
    }
    if (!columnExists($pdo, 'utilisateurs', 'bio')) {
        $pdo->exec("ALTER TABLE utilisateurs ADD bio TEXT NULL");
        echo "OK: utilisateurs.bio\n";
    }
    if (!columnExists($pdo, 'utilisateurs', 'competences')) {
        $pdo->exec("ALTER TABLE utilisateurs ADD competences VARCHAR(500) NULL");
        echo "OK: utilisateurs.competences\n";
    }

    if (!columnExists($pdo, 'services', 'delai_jours')) {
        $pdo->exec("ALTER TABLE services ADD delai_jours INT DEFAULT 7");
        echo "OK: services.delai_jours\n";
    }

    if (!columnExists($pdo, 'commandes', 'livrable_url')) {
        $pdo->exec("ALTER TABLE commandes ADD livrable_url VARCHAR(500) NULL");
        echo "OK: commandes.livrable_url\n";
    }
    if (!columnExists($pdo, 'commandes', 'date_livraison')) {
        $pdo->exec("ALTER TABLE commandes ADD date_livraison DATETIME NULL");
        echo "OK: commandes.date_livraison\n";
    }
    if (!columnExists($pdo, 'commandes', 'package_id')) {
        $pdo->exec("ALTER TABLE commandes ADD package_id INT NULL");
        echo "OK: commandes.package_id\n";
    }
    if (!columnExists($pdo, 'commandes', 'montant')) {
        $pdo->exec("ALTER TABLE commandes ADD montant DECIMAL(10,2) NULL");
        echo "OK: commandes.montant\n";
    }

    $pdo->exec("ALTER TABLE commandes MODIFY COLUMN statut ENUM('en_attente', 'acceptee', 'refusee', 'en_cours', 'livree', 'terminee') DEFAULT 'en_attente'");
    echo "OK: commandes.statut (étendu)\n";

    if (!tableExists($pdo, 'avis')) {
        $pdo->exec("CREATE TABLE avis (
            id INT AUTO_INCREMENT PRIMARY KEY,
            commande_id INT NOT NULL,
            client_id INT NOT NULL,
            freelance_id INT NOT NULL,
            note TINYINT NOT NULL,
            commentaire TEXT,
            date_avis DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_avis_commande (commande_id),
            FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
            FOREIGN KEY (client_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
            FOREIGN KEY (freelance_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "OK: table avis\n";
    }

    if (!tableExists($pdo, 'portfolio')) {
        $pdo->exec("CREATE TABLE portfolio (
            id INT AUTO_INCREMENT PRIMARY KEY,
            freelance_id INT NOT NULL,
            titre VARCHAR(150) NOT NULL,
            type_media ENUM('image','video','pdf','lien') DEFAULT 'image',
            url VARCHAR(500) NOT NULL,
            date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (freelance_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "OK: table portfolio\n";
    }

    if (!tableExists($pdo, 'packages')) {
        $pdo->exec("CREATE TABLE packages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            service_id INT NOT NULL,
            nom ENUM('basic','standard','premium') NOT NULL,
            prix DECIMAL(10, 2) NOT NULL,
            delai_jours INT DEFAULT 7,
            description TEXT,
            UNIQUE KEY unique_package (service_id, nom),
            FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "OK: table packages\n";
    }

    if (!tableExists($pdo, 'extras')) {
        $pdo->exec("CREATE TABLE extras (
            id INT AUTO_INCREMENT PRIMARY KEY,
            service_id INT NOT NULL,
            libelle VARCHAR(150) NOT NULL,
            prix DECIMAL(10, 2) NOT NULL DEFAULT 0,
            FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "OK: table extras\n";
    }

    if (!tableExists($pdo, 'messages')) {
        $pdo->exec("CREATE TABLE messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            commande_id INT NOT NULL,
            expediteur_id INT NOT NULL,
            destinataire_id INT NOT NULL,
            contenu TEXT NOT NULL,
            date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
            lu TINYINT(1) DEFAULT 0,
            FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
            FOREIGN KEY (expediteur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
            FOREIGN KEY (destinataire_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "OK: table messages\n";
    }

    if (!tableExists($pdo, 'offres_client')) {
        $pdo->exec("CREATE TABLE offres_client (
            id INT AUTO_INCREMENT PRIMARY KEY,
            client_id INT NOT NULL,
            titre VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            budget_max DECIMAL(10,2) NULL,
            date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
            statut ENUM('ouverte', 'fermee') DEFAULT 'ouverte',
            FOREIGN KEY (client_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
            INDEX idx_client (client_id),
            INDEX idx_statut (statut)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "OK: table offres_client\n";
    }

    if (!tableExists($pdo, 'participations_offre')) {
        $pdo->exec("CREATE TABLE participations_offre (
            id INT AUTO_INCREMENT PRIMARY KEY,
            offre_id INT NOT NULL,
            freelance_id INT NOT NULL,
            message TEXT NOT NULL,
            date_participation DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_participation (offre_id, freelance_id),
            FOREIGN KEY (offre_id) REFERENCES offres_client(id) ON DELETE CASCADE,
            FOREIGN KEY (freelance_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
            INDEX idx_offre (offre_id),
            INDEX idx_freelance (freelance_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "OK: table participations_offre\n";
    }

    echo "\n=== Migration terminée avec succès ===\n";
    echo "<a href='index.php'>Retour à l'accueil</a> | <a href='check_db.php'>Vérifier la BDD</a>\n";

} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
}

echo "</pre>";
