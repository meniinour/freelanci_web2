-- Migration Freelanci : TND, gouvernorats, avis, portfolio, packages, messagerie
-- Exécuter ce fichier sur une base freelanci existante (phpMyAdmin ou mysql)

USE freelanci;

-- Phase 1: Utilisateurs
ALTER TABLE utilisateurs 
  ADD COLUMN IF NOT EXISTS gouvernorat VARCHAR(80) NULL,
  ADD COLUMN IF NOT EXISTS bio TEXT NULL,
  ADD COLUMN IF NOT EXISTS competences VARCHAR(500) NULL;

-- Phase 1/2: Services (délai livraison)
ALTER TABLE services 
  ADD COLUMN IF NOT EXISTS delai_jours INT DEFAULT 7;

-- Phase 2: Avis (pour MySQL sans IF NOT EXISTS, ignorer si erreur "duplicate column")
CREATE TABLE IF NOT EXISTS avis (
  id INT AUTO_INCREMENT PRIMARY KEY,
  commande_id INT NOT NULL,
  client_id INT NOT NULL,
  freelance_id INT NOT NULL,
  note TINYINT NOT NULL CHECK (note >= 1 AND note <= 5),
  commentaire TEXT,
  date_avis DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_avis_commande (commande_id),
  FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
  FOREIGN KEY (client_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
  FOREIGN KEY (freelance_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Statuts commandes étendus
ALTER TABLE commandes MODIFY COLUMN statut ENUM('en_attente', 'acceptee', 'refusee', 'en_cours', 'livree', 'terminee') DEFAULT 'en_attente';
ALTER TABLE commandes ADD COLUMN IF NOT EXISTS livrable_url VARCHAR(500) NULL;
ALTER TABLE commandes ADD COLUMN IF NOT EXISTS date_livraison DATETIME NULL;

-- Phase 3: Portfolio
CREATE TABLE IF NOT EXISTS portfolio (
  id INT AUTO_INCREMENT PRIMARY KEY,
  freelance_id INT NOT NULL,
  titre VARCHAR(150) NOT NULL,
  type_media ENUM('image','video','pdf','lien') DEFAULT 'image',
  url VARCHAR(500) NOT NULL,
  date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (freelance_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
  INDEX idx_freelance (freelance_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Phase 4: Packages (Basic, Standard, Premium)
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

-- Phase 4: Extras
CREATE TABLE IF NOT EXISTS extras (
  id INT AUTO_INCREMENT PRIMARY KEY,
  service_id INT NOT NULL,
  libelle VARCHAR(150) NOT NULL,
  prix DECIMAL(10, 2) NOT NULL DEFAULT 0,
  FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Phase 5: Messagerie
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
  FOREIGN KEY (destinataire_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
  INDEX idx_commande (commande_id),
  INDEX idx_lu (lu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Commande: lien vers package choisi (optionnel)
ALTER TABLE commandes ADD COLUMN IF NOT EXISTS package_id INT NULL;
ALTER TABLE commandes ADD COLUMN IF NOT EXISTS montant DECIMAL(10,2) NULL;
