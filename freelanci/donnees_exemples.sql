-- ============================================================
-- DONNÉES D'EXEMPLE - Freelanci (site actif, style Fiverr/Upwork)
-- Exécuter après database.sql sur la base freelanci
-- Mot de passe pour tous les comptes démo : demo123
-- ============================================================
USE freelanci;

-- Hash bcrypt pour "demo123"
SET @mdp_demo = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

-- Freelances (proposent des services)
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, telephone, gouvernorat, type_utilisateur, bio, competences) VALUES
('Ben Ali', 'Sarra', 'sarra@freelanci.tn', @mdp_demo, '98123456', 'Tunis', 'freelance', 
 'Développeuse full-stack avec 5 ans d''expérience. Sites vitrines, e-commerce et applications sur mesure.', 
 'PHP, MySQL, React, Laravel, WordPress'),
('Tlili', 'Amine', 'amine@freelanci.tn', @mdp_demo, '97234567', 'Sfax', 'freelance',
 'Designer UI/UX et graphiste. Identité visuelle, logos et maquettes professionnelles.',
 'Figma, Photoshop, Illustrator, Branding'),
('Hadj', 'Karim', 'karim@freelanci.tn', @mdp_demo, '96345678', 'Sousse', 'freelance',
 'Rédacteur et expert SEO. Articles optimisés, traductions et contenu web.',
 'Rédaction SEO, Arabe, Français, Anglais')
ON DUPLICATE KEY UPDATE email = email;

-- Clients (passent des commandes)
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, telephone, gouvernorat, type_utilisateur) VALUES
('Chaabane', 'Aziz', 'aziz@freelanci.tn', @mdp_demo, '95456789', 'Tunis', 'client'),
('Mansouri', 'Leila', 'leila@freelanci.tn', @mdp_demo, '94567890', 'Nabeul', 'client')
ON DUPLICATE KEY UPDATE email = email;

-- Récupérer les IDs (après insert)
SET @sarra_id = (SELECT id FROM utilisateurs WHERE email = 'sarra@freelanci.tn' LIMIT 1);
SET @amine_id = (SELECT id FROM utilisateurs WHERE email = 'amine@freelanci.tn' LIMIT 1);
SET @karim_id = (SELECT id FROM utilisateurs WHERE email = 'karim@freelanci.tn' LIMIT 1);

-- Services proposés par les freelances (exemples variés)
INSERT INTO services (titre, description, prix, delai_jours, categorie_id, freelance_id, statut) VALUES
('Création de site web vitrine professionnel', 'Site vitrine responsive (5 pages), formulaire de contact, optimisé SEO. Livraison des maquettes + code source.', 800, 14, 1, @sarra_id, 'actif'),
('Application web sur mesure (PHP/MySQL)', 'Développement d''une application web avec back-office : authentification, tableau de bord, gestion des données.', 1500, 30, 1, @sarra_id, 'actif'),
('Logo et charte graphique complète', 'Logo vectoriel + variantes, palette de couleurs, typographies, carte de visite et en-tête. Fichiers sources fournis.', 350, 7, 2, @amine_id, 'actif'),
('Maquette UI/UX Figma (5 écrans)', 'Maquettes professionnelles pour site ou application : wireframes, prototype cliquable, guide de style.', 250, 5, 2, @amine_id, 'actif'),
('Pack identité visuelle startup', 'Logo + papeterie (carte, en-tête, signature mail) + 3 posts réseaux sociaux. Idéal pour lancement.', 500, 10, 2, @amine_id, 'actif'),
('Rédaction d''articles de blog SEO (5 articles)', 'Articles 500-800 mots, mots-clés fournis, structure H1-H3, meta description. Livraison sous 7 jours.', 120, 7, 3, @karim_id, 'actif'),
('Traduction professionnelle FR/AR/EN', 'Traduction de documents, sites ou contrats. Jusqu''à 2000 mots. Relecture incluse.', 80, 3, 3, @karim_id, 'actif'),
('Fiche produit e-commerce (10 fiches)', 'Descriptions produits optimisées SEO, titres accrocheurs, 150 mots par fiche. Pour boutique en ligne.', 200, 7, 3, @karim_id, 'actif')
ON DUPLICATE KEY UPDATE titre = titre;

-- Exemple de commande (client Aziz commande un logo à Amine)
SET @aziz_id = (SELECT id FROM utilisateurs WHERE email = 'aziz@freelanci.tn' LIMIT 1);
SET @service_logo_id = (SELECT id FROM services WHERE titre LIKE '%Logo et charte%' AND freelance_id = @amine_id LIMIT 1);

INSERT INTO commandes (service_id, client_id, freelance_id, message, statut) 
SELECT @service_logo_id, @aziz_id, @amine_id, 'Bonjour, j''ai besoin d''un logo pour ma startup tech. Couleurs : bleu et blanc. Merci de me proposer 2 pistes.', 'en_attente'
FROM DUAL
WHERE @service_logo_id IS NOT NULL AND @aziz_id IS NOT NULL
ON DUPLICATE KEY UPDATE message = message;

-- Fin des données d'exemple
-- Comptes démo : sarra@freelanci.tn, amine@freelanci.tn, karim@freelanci.tn (freelances) | aziz@freelanci.tn, leila@freelanci.tn (clients) | Mot de passe : demo123
