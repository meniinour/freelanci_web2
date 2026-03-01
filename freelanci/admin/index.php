<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

$pageTitle = 'Back Office - Accueil';
requireAdmin();

$pdo = getDBConnection();
$stmt = $pdo->query("SELECT COUNT(*) as total FROM utilisateurs WHERE type_utilisateur != 'admin'");
$total_users = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM services");
$total_services = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM commandes");
$total_commandes = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM categories");
$total_categories = $stmt->fetch()['total'];

include __DIR__ . '/../includes/header.php';
?>

<header class="masthead text-center text-white" style="padding-top: 100px; padding-bottom: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="masthead-content">
        <div class="container px-5">
            <h1 class="masthead-heading mb-0">Back Office</h1>
            <h2 class="masthead-subheading mb-0">Administration de Freelanci</h2>
        </div>
    </div>
</header>

<section style="padding: 80px 0;">
    <div class="container px-5">
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Bienvenue dans l'espace d'administration. Utilisez le menu pour gérer les différents éléments de la plateforme.
                </div>
            </div>
        </div>
        
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card shadow-lg border-primary">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-3x text-primary mb-3"></i>
                        <h3 class="text-primary"><?php echo $total_users; ?></h3>
                        <p class="mb-0">Utilisateurs</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-lg border-success">
                    <div class="card-body text-center">
                        <i class="fas fa-briefcase fa-3x text-success mb-3"></i>
                        <h3 class="text-success"><?php echo $total_services; ?></h3>
                        <p class="mb-0">Services</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-lg border-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-shopping-cart fa-3x text-warning mb-3"></i>
                        <h3 class="text-warning"><?php echo $total_commandes; ?></h3>
                        <p class="mb-0">Commandes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-lg border-info">
                    <div class="card-body text-center">
                        <i class="fas fa-tags fa-3x text-info mb-3"></i>
                        <h3 class="text-info"><?php echo $total_categories; ?></h3>
                        <p class="mb-0">Catégories</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card shadow-lg h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-users-cog fa-4x text-primary mb-3"></i>
                        <h5 class="card-title">Gestion des utilisateurs</h5>
                        <p class="card-text">Gérer les comptes freelances et clients</p>
                        <a href="utilisateurs.php" class="btn btn-primary">Accéder</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-lg h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-briefcase fa-4x text-success mb-3"></i>
                        <h5 class="card-title">Gestion des services</h5>
                        <p class="card-text">Ajouter, modifier ou supprimer des services</p>
                        <a href="services.php" class="btn btn-success">Accéder</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-lg h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-tags fa-4x text-info mb-3"></i>
                        <h5 class="card-title">Gestion des catégories</h5>
                        <p class="card-text">Gérer les catégories de services</p>
                        <a href="categories.php" class="btn btn-info">Accéder</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
