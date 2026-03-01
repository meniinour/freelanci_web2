# 📚 Documentation Complète du Projet Freelanci

## 🎯 Présentation du Projet

**Freelanci** est une plateforme web dynamique développée en PHP/MySQL permettant de mettre en relation les freelances et les clients. Le projet a été développé selon les exigences de l'énoncé du module Programmation Web II.

### Description générale

> **Freelanci** est une plateforme web intelligente destinée à mettre en relation les freelances et les clients à travers un espace digital sécurisé et interactif. Elle permet aux professionnels indépendants de proposer leurs services, et aux clients de publier leurs besoins afin de trouver rapidement des profils adaptés.
>
> La plateforme vise à faciliter le processus de recherche de prestations tout en offrant une expérience simple, fluide et professionnelle.

### Objectifs

- Digitaliser le marché du travail indépendant
- Réduire la distance entre offreurs et demandeurs de services
- Centraliser les services freelances dans une seule plateforme
- Garantir un environnement sécurisé grâce à l'authentification et aux sessions
- Offrir un système de gestion administrative via un Back-office

---

## 📋 Conformité avec l'Énoncé

### ✅ Exigences respectées

#### 1. Choix du thème
- ✅ Site web de vente en ligne d'un produit de votre choix
- **Notre choix** : Services freelance (inspiré de Fiverr/Upwork)

#### 2. Conception graphique
- ✅ Utilisation d'un template Bootstrap prédéfini
- ✅ Adaptation du design pour le thème freelance
- ✅ Interface moderne et responsive

#### 3. Menu du site
- ✅ **Accueil** (`index.php`) : Page principale avec présentation générale
- ✅ **Liste des Services** (`services.php`) : Affichage dynamique des services
- ✅ **Front Office** : Inscription, connexion, profil, commandes
- ✅ **Back Office** : Administration complète

#### 4. Front Office
- ✅ **Formulaire d'inscription** (`register.php`) :
  - Création de compte (freelance ou client)
  - Validation des données
  - Hashage des mots de passe
  
- ✅ **Formulaire de connexion** (`login.php`) :
  - Authentification sécurisée
  - Gestion des sessions
  - Redirection selon le type d'utilisateur
  
- ✅ **Formulaire de modification de profil** (`profile.php`) :
  - Modification des informations personnelles
  - Changement de mot de passe
  - Historique des commandes (pour les clients)
  
- ✅ **Formulaire de commande** (`services.php`) :
  - Modal de commande pour chaque service
  - Envoi de demande au freelance
  - Gestion des commandes

#### 5. Back Office
- ✅ **Authentification administrateur** :
  - Vérification du type d'utilisateur
  - Protection par sessions
  - Accès restreint
  
- ✅ **Page liste** :
  - Liste des utilisateurs (`admin/utilisateurs.php`)
  - Liste des services (`admin/services.php`)
  - Liste des catégories (`admin/categories.php`)
  
- ✅ **Page ajout** :
  - Formulaire d'ajout de service
  - Formulaire d'ajout de catégorie
  - (Utilisateurs créés via Front Office)
  
- ✅ **Page modification** :
  - Modification des utilisateurs
  - Modification des services
  - Modification des catégories
  
- ✅ **Page suppression** :
  - Suppression d'utilisateurs
  - Suppression de services
  - Suppression de catégories (avec vérification)

#### 6. Sessions
- ✅ Utilisation obligatoire de sessions PHP
- ✅ Protection de toutes les pages (sauf login/register)
- ✅ Gestion des droits d'accès par type d'utilisateur

#### 7. Base de données
- ✅ PHP + MySQL
- ✅ Connexion via PDO
- ✅ Requêtes préparées (sécurité)
- ✅ Structure normalisée

#### 8. Accès restreint
- ✅ "Le visiteur ne peut accéder au site web qu'après la création de son compte"
- ✅ Redirection automatique vers la page d'inscription

---

## 🏗️ Architecture Technique

### Structure des fichiers

```
startbootstrap-one-page-wonder-gh-pages/
│
├── admin/                          # Back Office
│   ├── index.php                  # Dashboard avec statistiques
│   ├── utilisateurs.php           # CRUD utilisateurs
│   ├── services.php               # CRUD services
│   └── categories.php             # CRUD catégories
│
├── assets/                        # Ressources statiques
│   └── img/                       # Images
│
├── config/                        # Configuration
│   ├── database.php               # Connexion BDD (PDO)
│   └── session.php                # Fonctions de gestion sessions
│
├── css/                           # Styles
│   └── styles.css                 # Bootstrap + styles personnalisés
│
├── includes/                      # Fichiers réutilisables
│   ├── header.php                 # En-tête avec navigation
│   └── footer.php                 # Pied de page
│
├── js/                            # Scripts JavaScript
│   └── scripts.js
│
├── database.sql                   # Script de création BDD
├── init_admin.php                 # Initialisation compte admin
├── index.php                      # Page d'accueil
├── login.php                      # Connexion
├── register.php                   # Inscription
├── logout.php                     # Déconnexion
├── services.php                   # Liste des services
├── profile.php                    # Profil utilisateur
├── mes-services.php               # Gestion services (freelance)
├── README.md                      # Documentation générale
├── GUIDE_INSTALLATION.md          # Guide d'installation
└── DOCUMENTATION_PROJET.md        # Ce fichier
```

### Base de données

#### Table `utilisateurs`
```sql
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- nom (VARCHAR 100)
- prenom (VARCHAR 100)
- email (VARCHAR 255, UNIQUE)
- mot_de_passe (VARCHAR 255) -- Hashé avec password_hash()
- telephone (VARCHAR 20, NULL)
- type_utilisateur (ENUM: 'freelance', 'client', 'admin')
- date_inscription (DATETIME, DEFAULT CURRENT_TIMESTAMP)
- statut (ENUM: 'actif', 'bloque')
```

#### Table `categories`
```sql
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- nom (VARCHAR 100, UNIQUE)
- description (TEXT, NULL)
- icone (VARCHAR 50, NULL) -- Nom icône Font Awesome
```

#### Table `services`
```sql
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- titre (VARCHAR 255)
- description (TEXT)
- prix (DECIMAL 10,2)
- image (VARCHAR 255, NULL)
- categorie_id (INT, FOREIGN KEY → categories.id)
- freelance_id (INT, FOREIGN KEY → utilisateurs.id)
- date_creation (DATETIME, DEFAULT CURRENT_TIMESTAMP)
- statut (ENUM: 'actif', 'inactif')
```

#### Table `commandes`
```sql
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- service_id (INT, FOREIGN KEY → services.id)
- client_id (INT, FOREIGN KEY → utilisateurs.id)
- freelance_id (INT, FOREIGN KEY → utilisateurs.id)
- message (TEXT, NULL)
- statut (ENUM: 'en_attente', 'acceptee', 'refusee', 'terminee')
- date_commande (DATETIME, DEFAULT CURRENT_TIMESTAMP)
```

### Technologies utilisées

- **Backend** : PHP 7.4+
- **Base de données** : MySQL 5.7+
- **Frontend** : HTML5, CSS3, JavaScript
- **Framework CSS** : Bootstrap 5.2.3
- **Icônes** : Font Awesome 6.3.0
- **Sessions** : PHP Sessions natives
- **Sécurité** : PDO avec requêtes préparées, password_hash()

---

## 🔐 Sécurité

### Mesures de sécurité implémentées

1. **Hashage des mots de passe**
   - Utilisation de `password_hash()` avec l'algorithme par défaut
   - Vérification avec `password_verify()`

2. **Protection contre les injections SQL**
   - Utilisation exclusive de requêtes préparées (PDO)
   - Pas de concaténation de chaînes dans les requêtes

3. **Protection XSS**
   - Utilisation de `htmlspecialchars()` sur toutes les sorties utilisateur
   - Échappement des données dans les formulaires

4. **Gestion des sessions**
   - Vérification de l'existence de la session avant accès
   - Destruction de session lors de la déconnexion
   - Protection des pages sensibles

5. **Contrôle d'accès**
   - Vérification du type d'utilisateur avant accès au Back Office
   - Redirection automatique si accès non autorisé

6. **Validation des données**
   - Validation côté serveur de tous les formulaires
   - Vérification des types de données
   - Contrôle des champs obligatoires

---

## 🎨 Fonctionnalités Détaillées

### Front Office

#### Pour les Freelances

1. **Inscription et connexion**
   - Création de compte avec type "freelance"
   - Authentification sécurisée

2. **Gestion de profil**
   - Modification des informations personnelles
   - Changement de mot de passe

3. **Gestion des services** (`mes-services.php`)
   - Ajout de nouveaux services
   - Modification de services existants
   - Suppression de services
   - Affichage de tous ses services

4. **Visualisation des demandes**
   - (Fonctionnalité extensible : voir les commandes reçues)

#### Pour les Clients

1. **Inscription et connexion**
   - Création de compte avec type "client"
   - Authentification sécurisée

2. **Gestion de profil**
   - Modification des informations personnelles
   - Changement de mot de passe
   - Consultation de l'historique des commandes

3. **Recherche de services** (`services.php`)
   - Affichage de tous les services disponibles
   - Filtrage par catégorie
   - Recherche par mots-clés
   - Affichage des détails (titre, description, prix, freelance)

4. **Commande de services**
   - Modal de commande pour chaque service
   - Envoi de message au freelance
   - Enregistrement de la demande en base de données

### Back Office

#### Dashboard (`admin/index.php`)

- Statistiques globales :
  - Nombre total d'utilisateurs
  - Nombre total de services
  - Nombre total de commandes
  - Nombre de catégories

- Liens rapides vers :
  - Gestion des utilisateurs
  - Gestion des services
  - Gestion des catégories

#### Gestion des utilisateurs (`admin/utilisateurs.php`)

- **Liste** : Affichage de tous les utilisateurs (sauf admins)
- **Filtre** : Par type (freelance/client)
- **Modification** :
  - Nom, prénom, email
  - Type d'utilisateur
  - Statut (actif/bloqué)
- **Suppression** : Suppression d'utilisateurs (sauf admin actuel)

#### Gestion des services (`admin/services.php`)

- **Liste** : Tous les services avec détails
- **Ajout** :
  - Titre, description, prix
  - Sélection du freelance
  - Sélection de la catégorie
  - Statut (actif/inactif)
- **Modification** : Modification de tous les champs
- **Suppression** : Suppression avec confirmation

#### Gestion des catégories (`admin/categories.php`)

- **Liste** : Toutes les catégories avec nombre de services
- **Ajout** :
  - Nom de la catégorie
  - Description
  - Icône Font Awesome
- **Modification** : Modification de tous les champs
- **Suppression** : Vérification que la catégorie n'est pas utilisée

---

## 📊 Points Clés pour la Soutenance

### 1. Présentation du projet

**Description à utiliser** :
> Freelanci est une plateforme web permettant de connecter freelances et clients, offrant un espace sécurisé pour publier, rechercher et gérer des services via un système Front Office et Back Office développé en PHP/MySQL.

### 2. Architecture

- **Séparation Front Office / Back Office** : Architecture claire et organisée
- **Réutilisabilité** : Fichiers includes (header, footer)
- **Modularité** : Fonctions dans config/session.php

### 3. Base de données

- **Normalisation** : Tables bien structurées avec relations
- **Intégrité référentielle** : Foreign keys et contraintes
- **Index** : Index sur les colonnes fréquemment utilisées

### 4. Sécurité

- **Sessions** : Utilisation obligatoire et sécurisée
- **Requêtes préparées** : Protection contre SQL injection
- **Hashage** : Mots de passe sécurisés
- **Validation** : Contrôle des données côté serveur

### 5. Fonctionnalités

- **CRUD complet** : Create, Read, Update, Delete pour tous les éléments
- **Formulaires** : Tous les formulaires demandés dans l'énoncé
- **Gestion des droits** : Différenciation freelance/client/admin

---

## 🎓 Réponses aux Questions Probables

### Q: Pourquoi avoir choisi le thème freelance ?

**R:** Le thème freelance est moderne, réaliste et permet d'illustrer parfaitement les concepts de programmation web : gestion d'utilisateurs multiples, relations entre tables, formulaires complexes, etc.

### Q: Comment fonctionnent les sessions ?

**R:** Les sessions PHP sont utilisées pour :
- Stocker l'ID de l'utilisateur connecté
- Stocker le type d'utilisateur (freelance/client/admin)
- Protéger l'accès aux pages
- Maintenir l'état de connexion

### Q: Comment êtes-vous protégé contre les injections SQL ?

**R:** Toutes les requêtes utilisent PDO avec des requêtes préparées. Les valeurs utilisateur sont passées en paramètres, jamais concaténées dans la requête.

### Q: Comment gérez-vous les différents types d'utilisateurs ?

**R:** Le champ `type_utilisateur` dans la table `utilisateurs` permet de différencier les rôles. Des fonctions dans `config/session.php` vérifient le type avant d'autoriser l'accès.

### Q: Quelle est la structure de votre base de données ?

**R:** 4 tables principales :
- `utilisateurs` : Tous les utilisateurs (freelances, clients, admins)
- `categories` : Catégories de services
- `services` : Services proposés par les freelances
- `commandes` : Demandes des clients

---

## 📝 Conclusion

Le projet **Freelanci** respecte intégralement les exigences de l'énoncé :

✅ Site web de vente en ligne (services)
✅ Menu complet avec toutes les sections
✅ Front Office avec tous les formulaires demandés
✅ Back Office avec authentification et CRUD complet
✅ Utilisation obligatoire de sessions
✅ PHP + MySQL
✅ Accès restreint (création de compte obligatoire)

Le projet illustre parfaitement l'application des concepts de programmation web à travers un projet concret, réaliste et orienté vers les besoins du marché actuel.

---

**Bon courage pour votre soutenance ! 🚀**
