# 🟦 Freelanci - Plateforme de Freelance

**Connecting talents to real opportunities**

## 📋 Description

Freelanci est une plateforme web dynamique développée en PHP/MySQL permettant de mettre en relation les freelances et les clients. Le site offre un espace sécurisé pour publier, rechercher et gérer des services freelance.

## 🎯 Fonctionnalités principales

### Front Office (Utilisateurs)

#### Pour les Freelances :
- ✅ Création de compte et authentification
- ✅ Création et gestion de profil professionnel
- ✅ Publication de services (titre, description, prix, catégorie)
- ✅ Modification et suppression de leurs services
- ✅ Visualisation des demandes reçues

#### Pour les Clients :
- ✅ Création de compte et authentification
- ✅ Consultation de la liste des services disponibles
- ✅ Recherche et filtrage des services par catégorie
- ✅ Envoi de demandes de prestation
- ✅ Consultation de l'historique des commandes

### Back Office (Administrateur)

- ✅ Authentification sécurisée
- ✅ Gestion des utilisateurs (ajout, modification, suppression, blocage)
- ✅ Gestion des services (ajout, modification, suppression)
- ✅ Gestion des catégories de services
- ✅ Tableau de bord avec statistiques
- ✅ Contrôle d'accès basé sur les sessions

## 🛠️ Technologies utilisées

- **Backend** : PHP 7.4+
- **Base de données** : MySQL 5.7+
- **Frontend** : HTML5, CSS3, JavaScript
- **Framework CSS** : Bootstrap 5
- **Icônes** : Font Awesome 6
- **Sessions** : PHP Sessions

## 📦 Installation

### Prérequis

- Serveur web (Apache/Nginx)
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Extension PHP PDO MySQL

### Étapes d'installation

1. **Cloner ou télécharger le projet**
   ```bash
   cd startbootstrap-one-page-wonder-gh-pages
   ```

2. **Configurer la base de données**
   
   - Créer une base de données MySQL
   - Modifier les paramètres de connexion dans `config/database.php` :
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'freelanci');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

3. **Importer la base de données**
   
   Exécutez le fichier SQL dans votre gestionnaire de base de données :
   ```bash
   mysql -u root -p freelanci < database.sql
   ```
   
   Ou importez `database.sql` via phpMyAdmin ou un autre outil.

4. **Données d'exemple (site actif, style Fiverr/Upwork)**
   
   Pour avoir des freelances, clients et services de démo :
   - Ouvrir **http://localhost/freelanci/freelanci/init_demo.php**
   - Tous les comptes démo ont le mot de passe : **demo123**
   - Freelances : sarra@freelanci.tn, amine@freelanci.tn, karim@freelanci.tn
   - Clients : aziz@freelanci.tn, leila@freelanci.tn

5. **Comment recevoir les demandes (freelances)**
   
   Les demandes des clients arrivent dans **Mon Profil → Commandes reçues**. Voir le guide complet : **Comment ça marche** (menu du site).

4. **Créer le compte administrateur**
   
   Le script SQL crée un compte admin par défaut, mais vous devez générer le hash du mot de passe.
   
   Exécutez `init_admin.php` dans votre navigateur ou via la ligne de commande :
   ```bash
   php init_admin.php
   ```
   
   Ou connectez-vous avec :
   - **Email** : `admin@freelanci.com`
   - **Mot de passe** : `admin123` (à changer après la première connexion)

5. **Configurer le serveur web**
   
   - Placez les fichiers dans le répertoire de votre serveur web
   - Assurez-vous que PHP peut écrire dans les sessions
   - Vérifiez les permissions des fichiers

## 🚀 Utilisation

### Accès au site

1. Ouvrez votre navigateur et accédez à : `http://localhost/freelanci/`
2. Créez un compte (freelance ou client)
3. Connectez-vous avec vos identifiants

### Accès au Back Office

1. Connectez-vous avec le compte administrateur
2. Cliquez sur "Back Office" dans le menu
3. Gérez les utilisateurs, services et catégories

## 📁 Structure du projet

```
startbootstrap-one-page-wonder-gh-pages/
│
├── admin/                    # Back Office
│   ├── index.php            # Dashboard admin
│   ├── utilisateurs.php     # Gestion utilisateurs
│   ├── services.php         # Gestion services
│   └── categories.php       # Gestion catégories
│
├── assets/                   # Ressources (images, etc.)
│   └── img/
│
├── config/                   # Configuration
│   ├── database.php         # Connexion BDD
│   └── session.php          # Gestion sessions
│
├── css/                      # Feuilles de style
│   └── styles.css
│
├── includes/                 # Fichiers inclus
│   ├── header.php           # En-tête commun
│   └── footer.php           # Pied de page commun
│
├── js/                       # Scripts JavaScript
│   └── scripts.js
│
├── database.sql              # Script SQL de création BDD
├── index.php                 # Page d'accueil
├── login.php                 # Page de connexion
├── register.php              # Page d'inscription
├── logout.php                # Déconnexion
├── services.php              # Liste des services
├── profile.php               # Profil utilisateur
├── mes-services.php          # Gestion services (freelance)
└── README.md                 # Ce fichier
```

## 🔐 Sécurité

- ✅ Mots de passe hashés avec `password_hash()` PHP
- ✅ Protection contre les injections SQL (requêtes préparées)
- ✅ Protection XSS (htmlspecialchars)
- ✅ Gestion des sessions sécurisées
- ✅ Contrôle d'accès par type d'utilisateur
- ✅ Validation des données côté serveur

## 📝 Conformité avec l'énoncé

Le projet respecte toutes les exigences de l'énoncé :

- ✅ Site web de vente en ligne (services freelance)
- ✅ Menu avec : Accueil, Liste des Services, Front Office, Back Office
- ✅ Front Office : formulaires d'inscription, modification profil, commande
- ✅ Back Office : authentification admin, gestion CRUD (services, utilisateurs)
- ✅ Utilisation obligatoire de sessions
- ✅ PHP + MySQL
- ✅ Accès restreint (création de compte obligatoire)

## 👥 Comptes de test

### Administrateur
- **Email** : `admin@freelanci.com`
- **Mot de passe** : `admin123`

### Créer des comptes de test
- Inscrivez-vous en tant que "freelance" pour proposer des services
- Inscrivez-vous en tant que "client" pour commander des services

## 🐛 Dépannage

### Erreur de connexion à la base de données
- Vérifiez les paramètres dans `config/database.php`
- Assurez-vous que MySQL est démarré
- Vérifiez que la base de données existe

### Erreur de session
- Vérifiez les permissions d'écriture du dossier de sessions PHP
- Vérifiez la configuration `session.save_path` dans php.ini

### Page blanche
- Activez l'affichage des erreurs PHP : `ini_set('display_errors', 1);`
- Vérifiez les logs d'erreur du serveur web

## 📄 Licence

Ce projet est développé dans le cadre d'un projet académique.

## 👨‍💻 Auteur

Développé pour le module Programmation Web II - IIT Université Nord Américaine

---

**Bon travail ! 🚀**
