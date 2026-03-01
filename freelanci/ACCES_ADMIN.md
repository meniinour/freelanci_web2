# Accès Admin (Back Office)

## Comment entrer en admin

### 1. Créer ou réinitialiser le compte admin

Ouvre dans le navigateur (une seule fois) :

**`http://localhost/freelanci/freelanci/init_admin.php`**

Cela crée (ou met à jour) le compte administrateur avec :

- **Email :** `admin@freelanci.com`
- **Mot de passe :** `admin123`

*(Adapte l’URL si ton site est dans un autre dossier.)*

### 2. Se connecter

1. Va sur la page **Connexion** du site.
2. Saisis **admin@freelanci.com** et **admin123**.
3. Valide : tu es redirigé automatiquement vers le **Back Office** (`admin/index.php`).

### 3. Accès direct

Une fois connecté en tant qu’admin, le lien **Back Office** apparaît dans le menu. Tu peux aussi aller directement sur :

**`http://localhost/freelanci/freelanci/admin/index.php`**

(Si tu n’es pas connecté en admin, tu seras renvoyé vers l’accueil.)

---

## Utilité du Back Office

L’espace admin permet de **gérer la plateforme** :

| Page | Utilité |
|------|--------|
| **Tableau de bord** (`admin/index.php`) | Vue d’ensemble : nombre d’utilisateurs, services, commandes, catégories. |
| **Gestion des utilisateurs** (`admin/utilisateurs.php`) | Lister les freelances et clients, bloquer/débloquer un compte, supprimer un utilisateur (sauf les admins). |
| **Gestion des services** (`admin/services.php`) | Voir tous les services, les activer/désactiver, modifier ou supprimer. |
| **Gestion des catégories** (`admin/categories.php`) | Ajouter, modifier ou supprimer les catégories de services (ex. Développement web, Design). |

En résumé : **contrôle central** sur les utilisateurs, les services et les catégories, sans passer par la base de données à la main.

---

**Sécurité :** après la première connexion, change le mot de passe admin (via Mon Profil ou en modifiant `init_admin.php` puis en le réexécutant).
