<?php
require_once __DIR__ . '/config/session.php';
$currentUser = getCurrentUser();
$baseAssets = (strpos($_SERVER['PHP_SELF'] ?? '', '/admin/') !== false) ? '../' : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Freelanci - Plateforme de mise en relation entre freelances et clients" />
    <meta name="author" content="Freelanci" />
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Freelanci</title>
    <link rel="icon" type="image/png" href="<?php echo $baseAssets; ?>assets/freelanci_logo.png" />
    <link rel="apple-touch-icon" href="<?php echo $baseAssets; ?>assets/freelanci_logo.png" />
    <!-- Font Awesome icons -->
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <!-- Google fonts - Mahara style -->
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap) -->
    <link href="css/styles.css" rel="stylesheet" />
    <style>
        :root {
            --accent-orange: #ff6b35;
            --accent-orange-hover: #ff8255;
        }
        body { font-family: 'DM Sans', sans-serif; }
        h1, h2, h3, .navbar-brand, .masthead-heading, .display-4 { font-family: 'Syne', sans-serif; }
        .navbar-brand { font-weight: 800; font-size: 1.5rem; display: flex; align-items: center; gap: 0.5rem; }
        .navbar-brand .logo-img { height: 72px; width: auto; max-width: 360px; object-fit: contain; }
        .btn-primary { background-color: var(--accent-orange) !important; border-color: var(--accent-orange) !important; }
        .btn-primary:hover { background-color: var(--accent-orange-hover) !important; border-color: var(--accent-orange-hover) !important; }
        .text-primary { color: var(--accent-orange) !important; }
        .user-menu { display: flex; align-items: center; gap: 15px; }
        .user-info { color: rgba(255,255,255,.75); font-size: 0.9rem; }
    </style>
</head>
<body id="page-top">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
        <div class="container px-5">
            <a class="navbar-brand" href="<?php echo $baseAssets; ?>index.php">
                <img src="<?php echo $baseAssets; ?>assets/freelanci_logo.png" alt="Freelanci" class="logo-img" />
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ms-auto w-100">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseAssets; ?>index.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseAssets; ?>services.php">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseAssets; ?>comment-ca-marche.php">Comment ça marche</a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $baseAssets; ?>messages.php">Messages</a>
                        </li>
                        <?php if (isFreelance()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $baseAssets; ?>mes-services.php">Mes Services</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $baseAssets; ?>mes-offres.php">Mes offres</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $baseAssets; ?>offres-clients.php">Offres des clients</a>
                            </li>
                        <?php endif; ?>
                        <?php if (isClient()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $baseAssets; ?>deposer-offre.php">Déposer une offre</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $baseAssets; ?>mes-offres-client.php">Mes offres</a>
                            </li>
                        <?php endif; ?>
                        <?php if (isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $baseAssets; ?>admin/index.php">Back Office</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item dropdown ms-auto">
                            <a class="nav-link dropdown-toggle user-info" href="#" id="navbarUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i>
                                <?php echo htmlspecialchars($currentUser['prenom'] . ' ' . $currentUser['nom']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarUserDropdown">
                                <li><a class="dropdown-item" href="<?php echo $baseAssets; ?>profile.php"><i class="fas fa-user me-2"></i>Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?php echo $baseAssets; ?>logout.php"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $baseAssets; ?>login.php">Connexion</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-success btn-sm rounded-pill px-3" href="<?php echo $baseAssets; ?>register.php">
                                <i class="fas fa-user-plus me-1"></i>Rejoindre
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
