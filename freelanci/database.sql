-- Base de données pour Freelanci
-- Plateforme de mise en relation freelances/clients

CREATE DATABASE IF NOT EXISTS freelanci CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE freelanci;

-- Table des utilisateurs (freelances et clients)
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    gouvernorat VARCHAR(80),
    bio TEXT,
    competences VARCHAR(500),
    type_utilisateur ENUM('freelance', 'client', 'admin') DEFAULT 'client',
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('actif', 'bloque') DEFAULT 'actif',
    INDEX idx_email (email),
    INDEX idx_type (type_utilisateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des catégories de services
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icone VARCHAR(50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des services (produits)
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    prix DECIMAL(10, 2) NOT NULL,
    delai_jours INT DEFAULT 7,
    image VARCHAR(255),
    categorie_id INT,
    freelance_id INT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('actif', 'inactif') DEFAULT 'actif',
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (freelance_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_freelance (freelance_id),
    INDEX idx_categorie (categorie_id),
    INDEX idx_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des commandes/demandes
CREATE TABLE IF NOT EXISTS commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    client_id INT NOT NULL,
    freelance_id INT NOT NULL,
    message TEXT,
    statut ENUM('en_attente', 'acceptee', 'refusee', 'en_cours', 'livree', 'terminee') DEFAULT 'en_attente',
    livrable_url VARCHAR(500),
    date_livraison DATETIME,
    package_id INT,
    montant DECIMAL(10,2),
    date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (freelance_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_client (client_id),
    INDEX idx_freelance (freelance_id),
    INDEX idx_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des avis
CREATE TABLE IF NOT EXISTS avis (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table portfolio (freelances)
CREATE TABLE IF NOT EXISTS portfolio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    freelance_id INT NOT NULL,
    titre VARCHAR(150) NOT NULL,
    type_media ENUM('image','video','pdf','lien') DEFAULT 'image',
    url VARCHAR(500) NOT NULL,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (freelance_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table packages (Basic, Standard, Premium)
CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    nom ENUM('basic','standard','premium') NOT NULL,
    prix DECIMAL(10, 2) NOT NULL,
    delai_jours INT DEFAULT 7,
    description TEXT,
    UNIQUE KEY unique_package (service_id, nom),
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table extras (options payantes)
CREATE TABLE IF NOT EXISTS extras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    libelle VARCHAR(150) NOT NULL,
    prix DECIMAL(10, 2) NOT NULL DEFAULT 0,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table messagerie
CREATE TABLE IF NOT EXISTS messages (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Offres déposées par les clients (style Fiverr)
CREATE TABLE IF NOT EXISTS offres_client (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Participations des freelances aux offres
CREATE TABLE IF NOT EXISTS participations_offre (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion des catégories par défaut
INSERT INTO categories (nom, description, icone) VALUES
('Développement Web', 'Sites web, applications web, e-commerce', 'code'),
('Design Graphique', 'Logos, identité visuelle, maquettes', 'palette'),
('Rédaction', 'Articles, traductions, rédaction SEO', 'pen'),
('Marketing Digital', 'SEO, réseaux sociaux, publicité', 'megaphone'),
('Montage Vidéo', 'Vidéos promotionnelles, montage, animation', 'video'),
('Consulting', 'Conseil stratégique, audit, formation', 'briefcase');

-- Création d'un administrateur par défaut
-- Email: admin@freelanci.com | Mot de passe: admin123
-- IMPORTANT: Exécutez init_admin.php pour générer le hash du mot de passe correctement
-- Le hash ci-dessous est un exemple et doit être régénéré
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, type_utilisateur) VALUES
('Admin', 'Freelanci', 'admin@freelanci.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE mot_de_passe = mot_de_passe;

-- Note: Exécutez init_admin.php après l'import pour mettre à jour le mot de passe admin
