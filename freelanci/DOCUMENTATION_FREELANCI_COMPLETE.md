# 📘 Documentation complète Freelanci — Projet Web II

**Plateforme de mise en relation Freelances / Clients**

Ce document regroupe : installation, conformité à l’énoncé, documentation de A à Z, pages et rôles, architecture POO, rappels de test, vérification de la connexion BDD, roadmap et guide de présentation.

---

## 📑 Table des matières

1. [Vue d’ensemble et exigences du projet](#1-vue-densemble-et-exigences-du-projet)
2. [Installation pas à pas](#2-installation-pas-à-pas)
3. [Conformité avec l’énoncé](#3-conformité-avec-lénoncé)
4. [Documentation de A à Z](#4-documentation-de-a-à-z)
5. [Toutes les pages (rôle et fichier)](#5-toutes-les-pages-rôle-et-fichier)
6. [Architecture POO — classes et méthodes](#6-architecture-poo--classes-et-méthodes)
7. [Rappels et guide de test (REMINDER)](#7-rappels-et-guide-de-test-reminder)
8. [Vérification de la connexion BDD](#8-vérification-de-la-connexion-bdd)
9. [Roadmap](#9-roadmap)
10. [Présentation / Soutenance](#10-présentation--soutenance)

---

# 1. Vue d’ensemble et exigences du projet

## Exigences du projet (rappel énoncé)

- **Menu** : Accueil, Liste des Produits/Services, Front Office (inscription, profil, commandes), Back Office.
- **Front Office** : Formulaire d’inscription, de connexion, de modification de profil, de commande de service.
- **Back Office** : Authentification administrateur, gestion des services (CRUD), gestion des utilisateurs, sessions.
- **Sécurité** : Accès restreint (création de compte obligatoire), sessions, protection des données.

**Technologies** : PHP 7.4+, MySQL 5.7+, PDO, Bootstrap 5, sessions PHP.

---

# 2. Installation pas à pas

## Préparer l’environnement

- Installer un serveur web local : XAMPP, WAMP, MAMP ou LAMP/LEMP.
- PHP 7.4+, MySQL 5.7+, extension PHP PDO activée.

## Placer les fichiers

- Copier le dossier du projet dans le répertoire du serveur (ex. XAMPP : `C:\xampp\htdocs\freelanci\freelanci\`).

## Configurer la base de données

1. Démarrer MySQL.
2. Créer la base `freelanci` (phpMyAdmin ou ligne de commande).
3. Importer `database.sql` dans la base `freelanci`.
4. Exécuter **une fois** : `http://localhost/freelanci/freelanci/install_migration.php` (tables et colonnes supplémentaires).

## Connexion BDD

Fichier `config/database.php` :

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'freelanci');
define('DB_USER', 'root');
define('DB_PASS', '');
```

## Initialiser le compte administrateur

- Ouvrir : `http://localhost/freelanci/freelanci/init_admin.php`
- Identifiants : **admin@freelanci.com** / **admin123** (à changer après première connexion).

## Tester l’installation

- Accéder à `http://localhost/freelanci/freelanci/`
- Créer un compte de test ou exécuter `init_demo.php` pour les données démo (mot de passe commun : **demo123**).

### Utilisation du site

**Visiteurs (non connectés)** : Conformément à l’énoncé, redirection vers inscription/connexion pour accéder au site. Création de compte (Client ou Freelance) puis connexion.

**Clients** : Menu Services → consulter et commander un service (modal) ; Mon Profil → modifier infos, mot de passe, voir l’historique des commandes ; Déposer une offre, Mes offres (voir participants).

**Freelances** : Mes Services → publier, modifier, supprimer des services ; Mes offres → voir les commandes reçues, accepter/refuser/livrer ; Offres des clients → participer ; Mes participations ; Messages.

**Administrateurs** : Back Office → tableau de bord (statistiques) ; Gestion des utilisateurs (liste, modifier, bloquer, supprimer) ; Gestion des services (CRUD) ; Gestion des catégories (CRUD, suppression si non utilisée).

### Structure des tables principales

- **utilisateurs** : id, nom, prenom, email, mot_de_passe, telephone, gouvernorat, type_utilisateur (freelance/client/admin), statut, date_inscription, bio, competences.
- **categories** : id, nom, description, icone.
- **services** : id, titre, description, prix, delai_jours, categorie_id, freelance_id, statut, date_creation.
- **commandes** : id, service_id, client_id, freelance_id, message, statut (en_attente, acceptee, refusee, en_cours, livree, terminee), livrable_url, date_commande.

### Sécurité et sessions

- Sessions PHP pour maintenir la connexion, protéger les pages, stocker les infos utilisateur.
- **Pages publiques** : login, register (et accueil/services/comment ça marche selon configuration).
- **Pages protégées** : toutes les autres (requireLogin).
- **Back Office** : requireAdmin().
- Fonctions : `requireLogin()`, `requireGuest()`, `requireAdmin()`.

### Dépannage rapide

- **Erreur connexion BDD** : MySQL démarré, paramètres dans `config/database.php`, base `freelanci` existante.
- **Page blanche** : vérifier les logs PHP, extensions activées.
- **Session** : permissions du dossier de sessions.
- **Admin inaccessible** : exécuter `init_admin.php`, vérifier que l’admin existe en BDD.

---

# 3. Conformité avec l’énoncé

## 1. Thème et sujet

| Énoncé | Où |
|--------|-----|
| Site web de vente en ligne d’un produit/service | Plateforme de **services freelance** (type Fiverr/Upwork). Vente de prestations. |

## 2. Conception graphique

| Énoncé | Où |
|--------|-----|
| Template Bootstrap | Template One Page Wonder (Bootstrap 5) dans `css/styles.css`. |
| Adaptation au thème | Thème personnalisé, `includes/header.php`, footer, couleurs logo. |
| Interface moderne et responsive | Grilles Bootstrap, navbar responsive, cartes. |

## 3. Menu du site

| Énoncé | Où |
|--------|-----|
| Accueil | `index.php` |
| Liste des produits/services | `services.php` |
| Front Office | Liens dans `includes/header.php` ; `register.php`, `login.php`, `profile.php`, commande dans `services.php`. |
| Back Office | Lien « Back Office » → `admin/index.php` (visible pour les admins). |

## 4. Front Office — Formulaires

| Énoncé | Où |
|--------|-----|
| Formulaire d’inscription | `register.php` — nom, prénom, email, mot de passe, téléphone, gouvernorat, type (client/freelance). |
| Formulaire de connexion | `login.php` — email, mot de passe, authentification, redirection selon le type. |
| Formulaire de modification de profil | `profile.php` — infos, mot de passe, bio, compétences, portfolio (freelance). |
| Formulaire de commande | `services.php` — bouton « Commander », modal avec message, table `commandes`. |

## 5. Back Office

| Énoncé | Où |
|--------|-----|
| Authentification administrateur | `requireAdmin()` dans les pages `admin/`. |
| Listes | `admin/utilisateurs.php`, `admin/services.php`, `admin/categories.php`. |
| Ajout / Modification / Suppression | CRUD dans chaque page admin. |

## 6. Sessions

| Énoncé | Où |
|--------|-----|
| Sessions PHP obligatoires | `config/session.php`, classe `Auth`, `requireLogin()`, `requireAdmin()`. |
| Protection des pages | `requireLogin()` sur les pages métier ; `requireAdmin()` pour l’admin. |
| Gestion des droits | `isFreelance()`, `isClient()`, `isAdmin()` ; menus conditionnels. |

## 7. Base de données

| Énoncé | Où |
|--------|-----|
| PHP + MySQL, PDO, requêtes préparées | `config/database.php`, classe `Database`, toutes les requêtes en `prepare()` + `execute()`. |
| Structure cohérente | Tables dans `database.sql` et `install_migration.php` ; clés étrangères. |

## 8. Accès restreint

| Énoncé | Où |
|--------|-----|
| Accès qu’après création de compte / connexion | Redirection vers login/inscription si non connecté sur les pages protégées. |

**Récapitulatif** : Thème, menu, formulaires Front/Back, sessions, BDD, accès restreint ✅. Le projet ajoute : offres clients (Fiverr), participations, messagerie, portfolio, design logo.

---

# 4. Documentation de A à Z

## A. Base de données et configuration

- **`config/database.php`** : Connexion PDO, constantes BDD.
- **`config/session.php`** : Session, `isLoggedIn()`, `requireLogin()`, `isAdmin()`, etc. (délégation à `Auth`).
- **`config/gouvernorats.php`** : Liste des 24 gouvernorats tunisiens.
- **`database.sql`** : Création des tables de base + extensions ; catégories par défaut ; compte admin.
- **`install_migration.php`** : Colonnes (gouvernorat, bio, competences, delai_jours, etc.) et tables (avis, portfolio, packages, extras, messages, offres_client, participations_offre).

## B. Inscription, connexion, profil

- **`register.php`** : Inscription (hash mot de passe), création compte.
- **`login.php`** : Connexion, session, redirection selon type.
- **`logout.php`** : Destruction session.
- **`profile.php`** : Modification profil, mot de passe, portfolio (freelance), commandes reçues (freelance), historique commandes (client).

## C. Pages principales

- **`index.php`** : Accueil, services en avant, recherche.
- **`services.php`** : Liste des services avec filtres, bouton « Commander » (modal).
- **`comment-ca-marche.php`** : Guide clients et freelances.

## D. Services (freelance)

- **`mes-services.php`** : Liste, ajout, modification, suppression de services.

## E. Commandes

- Création depuis `services.php` ; suivi dans **`mes-offres.php`** (freelance) et dans le profil (client).

## F. Offres clients (style Fiverr)

- **Tables** : `offres_client`, `participations_offre`.
- **`deposer-offre.php`** (client), **`mes-offres-client.php`**, **`participants-offre.php`**, **`offres-clients.php`** (freelance), **`mes-participations.php`**.

## G. Messagerie

- **Table** : `messages`. **`messages.php`** : conversations par contact (liées aux commandes).

## H. Back Office

- **`admin/index.php`** : Tableau de bord.
- **`admin/utilisateurs.php`**, **`admin/services.php`**, **`admin/categories.php`** : CRUD.

## I. En-tête, logo, design

- **`includes/header.php`** : Logo, menu selon rôle, dropdown utilisateur (Profil, Déconnexion). **`includes/footer.php`** : Pied de page.
- Logo : `assets/freelanci_logo.png` (favicon + navbar). Taille dans le header : 72px / 360px.

## J. Scripts utilitaires

- **`init_demo.php`** : Comptes démo, services, commande exemple, offres client (mot de passe : **demo123**).
- **`init_admin.php`** : Création / mise à jour mot de passe admin.
- **`check_db.php`** : Vérification connexion et tables (débogage).

---

# 5. Toutes les pages (rôle et fichier)

| Page | Fichier | Rôle |
|------|---------|------|
| Accueil | `index.php` | Présentation, 6 derniers services, recherche. |
| Services | `services.php` | Liste des services, filtres, bouton Commander (modal). |
| Comment ça marche | `comment-ca-marche.php` | Guide inscription, commande, réception demandes, messages. |
| Inscription | `register.php` | Création compte (client/freelance), hash mot de passe. |
| Connexion | `login.php` | Authentification, session, redirection selon type. |
| Déconnexion | `logout.php` | Destruction session. |
| Mon Profil | `profile.php` | Modification infos, mot de passe, portfolio (freelance), commandes reçues / passées. |
| Mes Services | `mes-services.php` | CRUD services (freelance). |
| Mes offres | `mes-offres.php` | Commandes reçues, accepter/refuser/livrer (freelance). |
| Déposer une offre | `deposer-offre.php` | Client : créer une offre. |
| Mes offres (client) | `mes-offres-client.php` | Liste offres déposées, voir participants, supprimer. |
| Participants à l’offre | `participants-offre.php` | Détail offre + liste freelances participants. |
| Offres des clients | `offres-clients.php` | Freelance : liste offres ouvertes, participer. |
| Mes participations | `mes-participations.php` | Freelance : offres auxquelles j’ai participé. |
| Messages | `messages.php` | Conversations par contact (liées aux commandes). |
| Back Office — Dashboard | `admin/index.php` | Statistiques, liens vers listes. |
| Back Office — Utilisateurs | `admin/utilisateurs.php` | Liste, modification, suppression. |
| Back Office — Services | `admin/services.php` | Liste, ajout, modification, suppression. |
| Back Office — Catégories | `admin/categories.php` | Liste, ajout, modification, suppression (avec vérification). |

---

# 6. Architecture POO — classes et méthodes

## Bootstrap et chargement

- **`bootstrap.php`** : Constantes BDD, autoload des classes (`classes/`).
- **`config/database.php`** : `getDBConnection()` → `Database::getInstance()->getConnection()`.
- **`config/session.php`** : Fonctions de session qui délèguent à **`Auth`**.

## Classes

| Classe | Rôle | Méthodes principales |
|--------|------|------------------------|
| **Database** | Singleton connexion PDO | `getInstance()`, `getConnection()`, `isConnected()` |
| **Auth** | Session et droits | `startSession()`, `login()`, `logout()`, `isLoggedIn()`, `isAdmin()`, `isFreelance()`, `isClient()`, `getCurrentUser()`, `getUserId()`, `requireLogin()`, `requireGuest()`, `requireAdmin()` |
| **Utilisateur** | Modèle utilisateurs | `find()`, `findByEmail()`, `emailExists()`, `create()`, `update()`, `delete()`, `getAll()` |
| **Categorie** | Modèle catégories | `getAll()`, `find()`, `create()`, `update()`, `delete()` |
| **Service** | Modèle services | `getActifs()`, `find()`, `getByFreelance()`, `create()`, `update()`, `delete()`, `getFreelanceId()`, `count()` |
| **Commande** | Modèle commandes | `create()`, `find()`, `getByUser()`, `getByClient()`, `getByFreelance()`, `updateStatut()`, `setLivrable()`, `terminerParClient()`, `getContactsForUser()`, `getFirstCommandeIdForContact()` |
| **Portfolio** | Modèle portfolio | `tableExists()`, `getByFreelance()`, `add()`, `delete()` |
| **Message** | Messagerie | `send()`, `getByCommande()`, `getByUserAndContact()`, `markAsRead()`, `markAsReadForUserAndContact()` |
| **OffreClient** | Offres clients | `tableExists()`, `getOuvertes()`, `getByClient()`, `find()`, `create()`, `delete()` |
| **ParticipationOffre** | Participations | `tableExists()`, `create()`, `hasParticipated()`, `getByOffre()`, `getByFreelance()` |

## Fonctions de session (`config/session.php`)

Elles enveloppent `Auth` : `isLoggedIn()`, `isAdmin()`, `isFreelance()`, `isClient()`, `requireLogin()`, `requireGuest()`, `requireAdmin()`, `getCurrentUser()`.

---

# 7. Rappels et guide de test (REMINDER)

## Pourquoi « Aucun service trouvé » ?

- Aucun service en base. **À faire** : exécuter `database.sql`, puis **`init_demo.php`** (crée 8 services + comptes démo).

## Migration

- Exécuter **une fois** : **`install_migration.php`** (tables `offres_client`, `participations_offre`, etc.).

## Comptes démo (mot de passe : **demo123**)

| Rôle | Email |
|------|--------|
| Freelance | sarra@freelanci.tn, amine@freelanci.tn, karim@freelanci.tn |
| Client | aziz@freelanci.tn, leila@freelanci.tn |

## Scénario test — offres clients

1. `install_migration.php` puis `init_demo.php`.
2. Client : aziz@freelanci.tn → Mes offres → Voir participants.
3. Freelance : amine@freelanci.tn → Offres des clients → Participer.

## Scénario test — Messages

- Une **commande** doit exister entre un client et un freelance. `init_demo.php` crée une commande Aziz → Amine.
- Client aziz → Messages → cliquer sur Amine Tlili → envoyer un message.
- Freelance amine → Messages → Aziz Chaabane → répondre.

## Fichiers importants

| Fichier | Rôle |
|---------|------|
| `config/database.php` | Connexion BDD |
| `config/session.php` | Session, droits |
| `includes/header.php` | Logo, menu |
| `init_demo.php` | Données démo |
| `install_migration.php` | Tables/colonnes |
| `messages.php` | Conversations par contact |

---

# 8. Vérification de la connexion BDD

## Méthode 1 : Script de test

- Accéder à **`check_db.php`** (ou `test_connection.php` si existant) : vérification connexion MySQL, base active, tables (utilisateurs, categories, services, commandes).

## Méthode 2 : phpMyAdmin

- Base `freelanci` visible.
- Tables : `utilisateurs`, `categories`, `services`, `commandes` (+ autres après migration).
- Au moins 1 utilisateur (admin), catégories présentes.

## Méthode 3 : Via le site

- `register.php` s’affiche sans erreur.
- Connexion avec admin@freelanci.com / admin123.
- Page `services.php` affiche les services (après `init_demo.php` si besoin).

## Problèmes courants

- **Erreur de connexion** : MySQL démarré ? Paramètres dans `config/database.php` corrects ? Base `freelanci` créée ?
- **Table n’existe pas** : importer `database.sql`, exécuter `install_migration.php`.
- **Access denied** : vérifier `DB_USER` et `DB_PASS` dans `config/database.php`.

## Commandes SQL utiles (phpMyAdmin)

```sql
USE freelanci;
SHOW TABLES;
SELECT COUNT(*) FROM utilisateurs;
SELECT * FROM utilisateurs WHERE type_utilisateur = 'admin';
```

---

# 9. Roadmap

## Déjà implémenté ✅

- Inscription / Connexion, accueil, recherche et filtres services, catégories, services (CRUD freelance), commandes, profil, admin (utilisateurs, services, catégories), offres clients, participations, messagerie, portfolio, avis, TND, gouvernorats, design (logo, Bootstrap).

## Phases possibles (optionnel)

- **Phase 1** : TND, gouvernorats, design (déjà en place).
- **Phase 2** : Avis, délai de livraison (présents).
- **Phase 3** : Portfolio, bio, compétences (présents).
- **Phase 4** : Packages Basic/Standard/Premium (tables et page `packages.php`).
- **Phase 5** : Messagerie (présente).
- **Phase 6** : Paiement (hors scope classique).

*Document à adapter selon le cours et l’énoncé.*

---

# 10. Présentation / Soutenance

## Ce que vous pouvez dire à l’oral

- **Énoncé** : « Le projet respecte l’énoncé : thème vente de services, menu (Accueil, Services, Front Office, Back Office), tous les formulaires (inscription, connexion, profil, commande), Back Office avec listes et CRUD, sessions obligatoires, accès restreint, PHP/MySQL et requêtes préparées. »
- **POO** : « Architecture orientée objet : classe `Database` (singleton), classe `Auth` pour la session, modèles (Utilisateur, Categorie, Service, Commande, Portfolio, Message, OffreClient, ParticipationOffre). Les fonctions dans session.php sont des wrappers vers Auth. »
- **Fonctionnalités en plus** : « Messagerie par contact, offres clients façon Fiverr avec participations, portfolio freelance, design cohérent avec le logo. »

## Récapitulatif pour la démo

1. Inscription / Connexion (client ou freelance).
2. Client : Services → Commander un service → Profil (historique).
3. Freelance : Mes Services (CRUD), Mes offres (accepter, livrer), Messages.
4. Offres clients : client dépose une offre ; freelance participe ; client voit les participants.
5. Admin : Back Office → Utilisateurs, Services, Catégories (CRUD).

## Conformité (rappel)

- Menu ✅ — Front Office (formulaires) ✅ — Back Office (auth, listes, CRUD) ✅ — Sessions ✅ — BDD (PDO, requêtes préparées) ✅ — Accès restreint ✅.

---

**Document unique généré pour Freelanci — Projet Web II. Bonne soutenance ! 🚀**
