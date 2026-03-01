<?php
require_once __DIR__ . '/config/database.php';

header('Content-Type: text/html; charset=utf-8');
$pdo = getDBConnection();
$mdp = password_hash('demo123', PASSWORD_DEFAULT);

$freelances = [
    ['Ben Ali', 'Sarra', 'sarra@freelanci.tn', '98123456', 'Tunis', 'Développeuse full-stack avec 5 ans d\'expérience. Sites vitrines, e-commerce et applications sur mesure.', 'PHP, MySQL, React, Laravel, WordPress'],
    ['Tlili', 'Amine', 'amine@freelanci.tn', '97234567', 'Sfax', 'Designer UI/UX et graphiste. Identité visuelle, logos et maquettes professionnelles.', 'Figma, Photoshop, Illustrator, Branding'],
    ['Hadj', 'Karim', 'karim@freelanci.tn', '96345678', 'Sousse', 'Rédacteur et expert SEO. Articles optimisés, traductions et contenu web.', 'Rédaction SEO, Arabe, Français, Anglais'],
];

$clients = [
    ['Chaabane', 'Aziz', 'aziz@freelanci.tn', '95456789', 'Tunis'],
    ['Mansouri', 'Leila', 'leila@freelanci.tn', '94567890', 'Nabeul'],
];

echo "<h1>Insertion des données d'exemple</h1><pre>";

foreach ($freelances as $f) {
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, telephone, gouvernorat, type_utilisateur, bio, competences) VALUES (?,?,?,?,?,?,'freelance',?,?) ON DUPLICATE KEY UPDATE mot_de_passe = ?, bio = ?, competences = ?");
    $stmt->execute([$f[0], $f[1], $f[2], $mdp, $f[3], $f[4], $f[5], $f[6], $mdp, $f[5], $f[6]]);
    echo "Freelance : $f[1] $f[0] ($f[2])\n";
}

foreach ($clients as $c) {
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, telephone, gouvernorat, type_utilisateur) VALUES (?,?,?,?,?,?,'client') ON DUPLICATE KEY UPDATE mot_de_passe = ?");
    $stmt->execute([$c[0], $c[1], $c[2], $mdp, $c[3], $c[4], $mdp]);
    echo "Client : $c[1] $c[0] ($c[2])\n";
}

$ids = [];
foreach (['sarra@freelanci.tn' => 'sarra', 'amine@freelanci.tn' => 'amine', 'karim@freelanci.tn' => 'karim', 'aziz@freelanci.tn' => 'aziz'] as $email => $k) {
    $stmt = $pdo->query("SELECT id FROM utilisateurs WHERE email = " . $pdo->quote($email));
    $ids[$k] = $stmt->fetchColumn();
}

$stmt = $pdo->query("SELECT COUNT(*) FROM services");
$nbServices = (int) $stmt->fetchColumn();
if ($nbServices == 0 && !empty($ids['sarra']) && !empty($ids['amine']) && !empty($ids['karim'])) {
    $services = [
        ['Création de site web vitrine professionnel', 'Site vitrine responsive (5 pages), formulaire de contact, optimisé SEO. Livraison des maquettes + code source.', 800, 14, 1, 'sarra'],
        ['Application web sur mesure (PHP/MySQL)', 'Développement d\'une application web avec back-office : authentification, tableau de bord, gestion des données.', 1500, 30, 1, 'sarra'],
        ['Logo et charte graphique complète', 'Logo vectoriel + variantes, palette de couleurs, typographies, carte de visite et en-tête. Fichiers sources fournis.', 350, 7, 2, 'amine'],
        ['Maquette UI/UX Figma (5 écrans)', 'Maquettes professionnelles pour site ou application : wireframes, prototype cliquable, guide de style.', 250, 5, 2, 'amine'],
        ['Pack identité visuelle startup', 'Logo + papeterie (carte, en-tête, signature mail) + 3 posts réseaux sociaux. Idéal pour lancement.', 500, 10, 2, 'amine'],
        ['Rédaction d\'articles de blog SEO (5 articles)', 'Articles 500-800 mots, mots-clés fournis, structure H1-H3, meta description. Livraison sous 7 jours.', 120, 7, 3, 'karim'],
        ['Traduction professionnelle FR/AR/EN', 'Traduction de documents, sites ou contrats. Jusqu\'à 2000 mots. Relecture incluse.', 80, 3, 3, 'karim'],
        ['Fiche produit e-commerce (10 fiches)', 'Descriptions produits optimisées SEO, titres accrocheurs, 150 mots par fiche. Pour boutique en ligne.', 200, 7, 3, 'karim'],
    ];
    foreach ($services as $s) {
        $stmt = $pdo->prepare("INSERT INTO services (titre, description, prix, delai_jours, categorie_id, freelance_id, statut) VALUES (?,?,?,?,?,?,'actif')");
        $stmt->execute([$s[0], $s[1], $s[2], $s[3], $s[4], $ids[$s[5]]]);
        echo "Service : $s[0]\n";
    }
} else {
    if ($nbServices > 0) echo "Services déjà présents ($nbServices). Pas d'ajout pour éviter les doublons.\n";
    else echo "Impossible d'ajouter les services (freelances démo manquants).\n";
}

$stmt = $pdo->prepare("SELECT id FROM services WHERE freelance_id = ? AND titre LIKE '%Logo et charte%' LIMIT 1");
$stmt->execute([$ids['amine']]);
$sid = $stmt->fetchColumn();
if ($sid && $ids['aziz']) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM commandes WHERE service_id = ? AND client_id = ?");
    $stmt->execute([$sid, $ids['aziz']]);
    if ($stmt->fetchColumn() == 0) {
        $pdo->prepare("INSERT INTO commandes (service_id, client_id, freelance_id, message, statut) VALUES (?,?,?,?,'en_attente')")->execute([$sid, $ids['aziz'], $ids['amine'], 'Bonjour, j\'ai besoin d\'un logo pour ma startup tech. Couleurs : bleu et blanc. Merci de me proposer 2 pistes.']);
        echo "Commande exemple créée (client Aziz -> freelance Amine)\n";
    }
}

try {
    $pdo->query("SELECT 1 FROM offres_client LIMIT 1");
    $nbOffres = (int) $pdo->query("SELECT COUNT(*) FROM offres_client")->fetchColumn();
    if ($nbOffres == 0 && !empty($ids['aziz'])) {
        $pdo->prepare("INSERT INTO offres_client (client_id, titre, description, budget_max, statut) VALUES (?,?,?,?,?)")
            ->execute([$ids['aziz'], 'Refonte site vitrine + SEO', 'Je cherche un freelance pour refondre mon site vitrine (5-6 pages) et optimiser le référencement. Budget flexible selon expérience.', 1200, 'ouverte']);
        $pdo->prepare("INSERT INTO offres_client (client_id, titre, description, budget_max, statut) VALUES (?,?,?,?,?)")
            ->execute([$ids['aziz'], 'Logo et charte graphique startup', 'Création logo + charte pour une startup tech. Couleurs : bleu et blanc. Livraison sous 2 semaines.', 400, 'ouverte']);
        echo "Offres client (démo) créées : 2 offres pour Aziz.\n";
    }
} catch (Exception $e) {}

echo "</pre><p><strong>Terminé.</strong> Tous les comptes démo ont le mot de passe : <kbd>demo123</kbd></p>";
echo "<p><a href='index.php'>Accueil</a> | <a href='services.php'>Services</a> | <a href='login.php'>Connexion</a></p>";
echo "<p><strong>Freelances :</strong> sarra@freelanci.tn, amine@freelanci.tn, karim@freelanci.tn — <strong>Clients :</strong> aziz@freelanci.tn, leila@freelanci.tn</p>";
echo "<p>Clients : <a href='deposer-offre.php'>Déposer une offre</a> | <a href='mes-offres-client.php'>Mes offres</a> — Freelances : <a href='offres-clients.php'>Offres des clients</a> | <a href='mes-participations.php'>Mes participations</a></p>";
