# 🎤 Guide de Soutenance - Freelanci — Projet Web II

**Ce document vérifie le projet par rapport à l’énoncé (Projet Web II) et indique pour chaque partie de l’énoncé comment elle est réalisée.**

> Si vous avez le fichier **« Enoncé projet Web II.pdf »**, vous pouvez suivre ce guide point par point : chaque exigence typique de l’énoncé est listée ci‑dessous avec le fichier ou la fonctionnalité qui la couvre.

---

# Partie A — Vérification avec l’énoncé (chaque partie expliquée)

Les énoncés du Projet Web II demandent en général les points suivants. Pour **chaque partie**, on indique : **ce que demande l’énoncé** puis **comment c’est fait dans Freelanci** (fichiers, comportement).

---

## 1. Thème et sujet du site

**Ce que demande l’énoncé :**  
Site web de vente en ligne d’un produit ou d’un **service** de votre choix.

**Comment c’est fait dans Freelanci :**  
- Thème choisi : **plateforme de services freelance** (type Fiverr/Upwork).  
- On ne vend pas un produit physique mais des **prestations** : création de site, logo, rédaction SEO, etc.  
- Les **freelances** proposent des services ; les **clients** les commandent.  
- Fichiers concernés : toute l’application ; le catalogue de « produits » = **liste des services** (`services.php`).

---

## 2. Conception graphique

**Ce que demande l’énoncé :**  
- Utilisation d’un **template Bootstrap** prédéfini.  
- Adaptation du design au **thème** choisi.  
- Interface **moderne et responsive**.

**Comment c’est fait dans Freelanci :**  
- **Template** : Bootstrap 5 (thème One Page Wonder) dans `css/styles.css`.  
- **Adaptation au thème** : couleurs et style alignés sur le logo Freelanci (navbar, boutons, liens) ; fichiers `includes/header.php`, `css/override-primary-red.css` ou variables CSS dans le header.  
- **Responsive** : grilles Bootstrap, navbar repliable, cartes et formulaires adaptés mobile.  
- **Logo** : `assets/freelanci_logo.png` utilisé dans la navbar et en favicon.

---

## 3. Menu du site

**Ce que demande l’énoncé :**  
Menu contenant au minimum : **Accueil**, **Liste des produits/services**, **Front Office** (inscription, connexion, profil, commandes), **Back Office**.

**Comment c’est fait dans Freelanci :**  
- **Accueil** : lien vers `index.php` (présentation, services en avant, recherche).  
- **Liste des services** : lien vers `services.php` (catalogue avec filtres et commande).  
- **Comment ça marche** : `comment-ca-marche.php` (guide utilisateur).  
- **Front Office** (selon rôle) :  
  - Pour **tout le monde connecté** : Profil, Messages (dropdown sur le nom dans `includes/header.php`).  
  - Pour **freelance** : Mes Services, Mes offres, Offres des clients.  
  - Pour **client** : Déposer une offre, Mes offres.  
  - Pour **non connecté** : Connexion, Rejoindre (inscription).  
- **Back Office** : lien « Back Office » visible uniquement pour les **admins** → `admin/index.php`.  
- Le menu est construit dans `includes/header.php` avec `$baseAssets` pour gérer le préfixe depuis `admin/`.

---

## 4. Front Office — Formulaires demandés

### 4.1 Formulaire d’inscription

**Ce que demande l’énoncé :**  
Formulaire d’inscription pour créer un compte (données personnelles, type d’utilisateur).

**Comment c’est fait dans Freelanci :**  
- **Fichier** : `register.php`.  
- **Champs** : nom, prénom, email, mot de passe (avec confirmation), téléphone, gouvernorat (liste des 24 gouvernorats tunisiens dans `config/gouvernorats.php`), **type** (client ou freelance).  
- **Traitement** : vérification que l’email n’existe pas (`Utilisateur::emailExists()`), création du compte avec `Utilisateur::create()` ; mot de passe hashé avec `password_hash()`.  
- **Sécurité** : requêtes préparées, validation côté serveur.  
- Après création, redirection vers la page de connexion ou accueil.

### 4.2 Formulaire de connexion

**Ce que demande l’énoncé :**  
Formulaire de connexion ; authentification ; redirection selon le type d’utilisateur.

**Comment c’est fait dans Freelanci :**  
- **Fichier** : `login.php`.  
- **Champs** : email, mot de passe.  
- **Traitement** : récupération de l’utilisateur par email (`Utilisateur::findByEmail()`), vérification du mot de passe avec `password_verify()`, vérification du statut (non bloqué).  
- **Session** : remplie via `Auth::login($user)` (id, nom, prénom, email, type).  
- **Redirection** : si type **admin** → `admin/index.php` ; sinon → `index.php`.  
- Les visiteurs déjà connectés sont redirigés grâce à `Auth::requireGuest()`.

### 4.3 Formulaire de modification de profil

**Ce que demande l’énoncé :**  
Possibilité de modifier ses informations personnelles (et éventuellement le mot de passe).

**Comment c’est fait dans Freelanci :**  
- **Fichier** : `profile.php`.  
- **Accès** : réservé aux utilisateurs connectés (`Auth::requireLogin()`).  
- **Modification des infos** : nom, prénom, email, téléphone, gouvernorat, bio, compétences ; mise à jour avec `Utilisateur::update()`.  
- **Changement de mot de passe** : formulaire séparé (ancien mot de passe, nouveau, confirmation) ; vérification puis mise à jour via `Utilisateur::update()`.  
- **En plus** : pour les **freelances**, gestion du **portfolio** (ajout/suppression d’éléments avec `Portfolio::add()`, `Portfolio::delete()`) et section **commandes reçues** (accepter, refuser, marquer livrée) ; pour les **clients**, historique des commandes et lien pour laisser un avis.

### 4.4 Formulaire de commande

**Ce que demande l’énoncé :**  
Formulaire permettant de passer une commande (demande) sur un produit/service.

**Comment c’est fait dans Freelanci :**  
- **Fichier** : `services.php`.  
- **Affichage** : liste des services actifs (avec filtres : catégorie, prix, délai, recherche, tri) ; pour chaque service, bouton **« Commander »**.  
- **Commande** : au clic sur « Commander », ouverture d’un **modal** avec un champ message (demande du client).  
- **Traitement** : récupération du `service_id`, du `client_id` (session), du `freelance_id` (`Service::getFreelanceId()`), enregistrement en base avec `Commande::create($serviceId, $clientId, $freelanceId, $message)`.  
- La commande apparaît ensuite dans **Mes offres** pour le freelance et dans le **profil** du client.

---

## 5. Back Office

**Ce que demande l’énoncé :**  
- **Authentification** réservée à l’administrateur.  
- **Listes** des entités à gérer (ex. utilisateurs, produits/services, catégories).  
- **Ajout**, **modification**, **suppression** de ces entités.

**Comment c’est fait dans Freelanci :**  
- **Authentification admin** : toutes les pages sous `admin/` appellent `requireAdmin()` (défini dans `config/session.php`, qui délègue à `Auth::requireAdmin()`). Si l’utilisateur n’est pas de type `admin`, redirection vers l’accueil.  
- **Tableau de bord** : `admin/index.php` — statistiques (nombre d’utilisateurs, services, commandes, catégories) et liens vers les listes.  
- **Liste des utilisateurs** : `admin/utilisateurs.php` — tableau avec filtre par type (freelance/client) ; colonnes : ID, nom, prénom, email, type, statut, date d’inscription, actions (modifier, supprimer). On n’affiche pas les comptes admin ; on ne peut pas se supprimer soi‑même.  
- **Liste des services** : `admin/services.php` — tous les services avec freelance, catégorie, prix, statut ; actions modifier, supprimer ; **formulaire d’ajout** en haut (titre, description, prix, freelance, catégorie, statut).  
- **Liste des catégories** : `admin/categories.php` — toutes les catégories avec nombre de services ; **formulaire d’ajout** (nom, description, icône) ; modification et suppression (la suppression est refusée si la catégorie est utilisée par des services).  
- **Modification** : dans chaque page admin, lien « Modifier » qui affiche un formulaire prérempli ; envoi en POST avec mise à jour en BDD (requêtes préparées).  
- **Suppression** : lien/bouton « Supprimer » avec confirmation JavaScript ; suppression en BDD (avec les contraintes indiquées ci‑dessus).

---

## 6. Sessions

**Ce que demande l’énoncé :**  
Utilisation **obligatoire** des sessions PHP pour gérer la connexion et les droits.

**Comment c’est fait dans Freelanci :**  
- **Démarrage de la session** : `Auth::startSession()` appelé dans `config/session.php`, lui‑même inclus via `config/database.php` (ou directement) sur les pages qui en ont besoin.  
- **Contenu de la session** : identifiant utilisateur, type (freelance/client/admin), nom, prénom, email (remplis lors du login dans `Auth::login()`).  
- **Vérifications** :  
  - `isLoggedIn()` : vrai si un utilisateur est connecté.  
  - `isAdmin()`, `isFreelance()`, `isClient()` : selon le type en session.  
  - `requireLogin()` : si non connecté, redirection vers `login.php`.  
  - `requireAdmin()` : si non admin, redirection (ex. vers `index.php`).  
  - `requireGuest()` : utilisé sur `login.php` et `register.php` pour rediriger si déjà connecté.  
- **Déconnexion** : `logout.php` appelle `Auth::logout()` (destruction de la session) puis redirige vers l’accueil.  
- Les fonctions de session sont définies dans `config/session.php` et délèguent à la classe `Auth` (POO).

---

## 7. Base de données

**Ce que demande l’énoncé :**  
- Utilisation de **PHP** et **MySQL**.  
- Connexion via **PDO**.  
- **Requêtes préparées** pour la sécurité (pas de concaténation SQL avec les données utilisateur).  
- Structure **cohérente** (tables, relations, clés étrangères).

**Comment c’est fait dans Freelanci :**  
- **Connexion** : `config/database.php` définit les constantes (host, base, user, mot de passe) et inclut `bootstrap.php`. La connexion PDO est centralisée dans la classe **`Database`** (singleton) ; la fonction `getDBConnection()` retourne cette connexion.  
- **Requêtes préparées** : partout dans le projet — `$pdo->prepare()` puis `execute()` avec des paramètres ; aucune concaténation de valeurs utilisateur dans le SQL.  
- **Tables principales** (schéma dans `database.sql` et évolutions dans `install_migration.php`) :  
  - **utilisateurs** : id, nom, prénom, email, mot_de_passe, telephone, gouvernorat, type_utilisateur (freelance/client/admin), statut, date_inscription, bio, competences…  
  - **categories** : id, nom, description, icône…  
  - **services** : id, titre, description, prix, delai_jours, categorie_id, freelance_id, statut, date_creation…  
  - **commandes** : id, service_id, client_id, freelance_id, message, statut, livrable_url, date_livraison…  
- **Tables complémentaires** : avis, portfolio, packages, extras, messages, offres_client, participations_offre (créées ou complétées par `install_migration.php`).  
- **Relations** : clés étrangères (ex. `services.freelance_id` → `utilisateurs.id`, `commandes.service_id` → `services.id`).  
- **POO** : les accès BDD sont encapsulés dans des classes (`Utilisateur`, `Categorie`, `Service`, `Commande`, `Portfolio`, `Message`, `OffreClient`, `ParticipationOffre`) qui utilisent toutes `Database::getInstance()->getConnection()`.

---

## 8. Accès restreint

**Ce que demande l’énoncé :**  
« Le visiteur ne peut accéder au site web qu’après la création de son compte » (ou après connexion).

**Comment c’est fait dans Freelanci :**  
- Les pages **publiques** (accessibles sans être connecté) sont limitées à : **Accueil** (`index.php`), **Services** (`services.php` — consultation et recherche), **Comment ça marche** (`comment-ca-marche.php`), **Connexion** (`login.php`), **Inscription** (`register.php`).  
- **Toutes les autres pages** (profil, messages, mes services, mes offres, dépôt d’offre, etc.) appellent `requireLogin()` au début ; si l’utilisateur n’est pas connecté, il est **redirigé** vers la page de connexion (ou inscription).  
- Ainsi, un visiteur non connecté ne peut pas accéder au cœur du site sans créer un compte et se connecter.  
- Les pages **admin** ajoutent en plus `requireAdmin()` : seul un compte de type `admin` peut y accéder.

---

# Partie B — Récapitulatif par numéro d’exigence

| N° | Partie de l’énoncé | Où c’est fait |
|----|--------------------|---------------|
| 1 | Thème : vente de service | Plateforme freelance ; services dans `services.php`. |
| 2 | Template Bootstrap + design adapté | `css/styles.css`, header/footer, couleurs logo. |
| 3 | Menu : Accueil | `index.php`, lien dans `includes/header.php`. |
| 4 | Menu : Liste des services | `services.php`. |
| 5 | Menu : Front Office (inscription, connexion, profil, commandes) | Liens dans le header ; `register.php`, `login.php`, `profile.php`, commande dans `services.php`. |
| 6 | Menu : Back Office | Lien « Back Office » → `admin/index.php` (visible si admin). |
| 7 | Formulaire d’inscription | `register.php` (Utilisateur::create, password_hash). |
| 8 | Formulaire de connexion | `login.php` (Auth::login, redirection selon type). |
| 9 | Formulaire de modification de profil | `profile.php` (Utilisateur::update, portfolio, commandes). |
| 10 | Formulaire de commande | `services.php` (modal + Commande::create). |
| 11 | Back Office : authentification admin | `requireAdmin()` dans toutes les pages `admin/`. |
| 12 | Back Office : listes | `admin/utilisateurs.php`, `admin/services.php`, `admin/categories.php`. |
| 13 | Back Office : ajout / modification / suppression | CRUD dans les trois pages admin. |
| 14 | Sessions obligatoires | `config/session.php`, `Auth`, `requireLogin()` sur les pages protégées. |
| 15 | Accès restreint (accès qu’après création de compte) | Redirection vers login si non connecté sur les pages métier. |
| 16 | PHP + MySQL, PDO, requêtes préparées | `config/database.php`, classe `Database`, toutes les requêtes en `prepare()` + `execute()`. |

---

# Partie C — Introduction et déroulé oral (soutenance)

## Introduction (1–2 minutes)

- Présenter **Freelanci** : plateforme web PHP/MySQL pour mettre en relation freelances et clients.  
- Objectifs : digitaliser la mise en relation, centraliser les services, garantir un accès sécurisé (authentification, sessions), offrir un Back Office d’administration.  
- Indiquer que le projet respecte l’énoncé du Projet Web II et que chaque point est couvert (référence à la Partie A et au tableau de la Partie B).

## Démonstration (5–7 minutes)

- **Inscription / Connexion** : création d’un compte (client ou freelance), connexion, redirection selon le type.  
- **Client** : parcourir les services, commander un service (modal), voir l’historique dans le profil.  
- **Freelance** : Mes Services (ajout/modification/suppression), Mes offres (accepter, refuser, livrer), éventuellement Messages et offres clients.  
- **Admin** : connexion avec un compte admin, Back Office, listes utilisateurs / services / catégories, ajout, modification, suppression.

## Points forts à souligner

- Conformité à l’énoncé : menu, formulaires Front Office, Back Office avec CRUD, sessions, accès restreint, PDO et requêtes préparées.  
- Architecture POO : classes `Database`, `Auth`, modèles (Utilisateur, Service, Commande, etc.), voir `README_POO.md` ou `GUIDE_PRESENTATION_SOUTENANCE.md`.  
- Sécurité : hash des mots de passe, requêtes préparées, `htmlspecialchars()` sur les sorties, gestion stricte des sessions.  
- En plus de l’énoncé : messagerie par contact, offres clients (style Fiverr), participations, portfolio, design cohérent avec le logo.

## Barème (rappel typique)

- Présentation et réponses aux questions : 6 pts  
- Front-office (ergonomie, formulaires, connexion BDD) : 5 pts  
- Back-office (authentification, sessions, CRUD) : 9 pts  
- **Total : 20 points**

---

# Partie D — Checklist avant la soutenance

- [ ] Tester inscription, connexion, déconnexion (client, freelance, admin).  
- [ ] Vérifier que la base est importée (`database.sql`) et que les migrations sont appliquées (`install_migration.php`).  
- [ ] Tester la commande d’un service, la réception côté freelance, accepter/livrer.  
- [ ] Tester le Back Office : listes, ajout, modification, suppression (utilisateurs, services, catégories).  
- [ ] Avoir des comptes de test (ex. `init_demo.php`, `init_admin.php`) et le mot de passe admin.  
- [ ] Vérifier que les pages protégées redirigent bien si non connecté et que l’admin est réservé aux admins.

---

**Référence** : Si votre énoncé officiel (« Enoncé projet Web II.pdf ») contient des numéros ou des libellés différents, reportez‑les dans la colonne « Partie de l’énoncé » et remplissez « Où c’est fait » à partir des explications de la Partie A. Bonne soutenance.
