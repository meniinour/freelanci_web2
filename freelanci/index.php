<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';

$pageTitle = 'Accueil';
$services = Service::getActifs(['tri' => 'date']);
$services = array_slice($services, 0, 6);
$categories = Categorie::getAll();

include __DIR__ . '/includes/header.php';
?>

<header class="masthead text-center text-white">
    <div class="masthead-content">
        <div class="container px-5">
            <h1 class="masthead-heading mb-0"></h1>
            <h2 class="masthead-subheading mb-0">Nos freelances s'en occupent pour vous</h2>
            <p class="lead mt-3">Trouvez le talent idéal pour votre projet</p>
            
            <form method="GET" action="services.php" class="mt-4 mx-auto" style="max-width: 600px;">
                <div class="input-group input-group-lg shadow">
                    <input type="text" class="form-control" name="search" placeholder="Ex: logo, site web, rédaction SEO..." aria-label="Rechercher un service">
                    <button class="btn btn-primary px-4" type="submit">
                        <i class="fas fa-search me-2"></i>Rechercher
                    </button>
                </div>
            </form>
            
            <div class="mt-4">
                <?php if (!isLoggedIn()): ?>
                    <a class="btn btn-outline-light btn-lg rounded-pill me-2" href="login.php">
                        <i class="fas fa-sign-in-alt me-2"></i>Connexion
                    </a>
                    <a class="btn btn-success btn-lg rounded-pill" href="register.php">
                        <i class="fas fa-user-plus me-2"></i>Rejoindre
                    </a>
                <?php else: ?>
                    <a class="btn btn-primary btn-xl rounded-pill" href="services.php">
                        <i class="fas fa-search me-2"></i>Découvrir les services
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="bg-circle-1 bg-circle"></div>
    <div class="bg-circle-2 bg-circle"></div>
    <div class="bg-circle-3 bg-circle"></div>
    <div class="bg-circle-4 bg-circle"></div>
</header>

<section class="py-5 bg-light">
    <div class="container px-5">
        <div class="text-center mb-5">
            <h2 class="display-5">Comment ça marche</h2>
            <p class="lead text-muted">Plateforme professionnelle : inscription, commande, réception des demandes</p>
        </div>
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;font-size:1.5rem;">1</div>
                        <h5>Inscrivez-vous</h5>
                        <p class="text-muted small mb-0">Créez un compte (client ou freelance). Accès au site après connexion, conformément à l'énoncé.</p>
                        <a href="comment-ca-marche.php" class="btn btn-sm btn-outline-primary mt-3">En savoir plus</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;font-size:1.5rem;">2</div>
                        <h5>Commandez ou proposez</h5>
                        <p class="text-muted small mb-0">Clients : commandez un service. Freelances : publiez vos services et recevez les demandes dans votre profil.</p>
                        <a href="comment-ca-marche.php#recevoir" class="btn btn-sm btn-outline-primary mt-3">Recevoir les demandes</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;font-size:1.5rem;">3</div>
                        <h5>Échangez et livrez</h5>
                        <p class="text-muted small mb-0">Messagerie intégrée, livrable, confirmation client et avis. Suivi complet des commandes.</p>
                        <a href="services.php" class="btn btn-sm btn-outline-primary mt-3">Voir les services</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="comment-ca-marche.php" class="btn btn-primary btn-lg"><i class="fas fa-book me-2"></i>Guide complet — Comment recevoir les demandes</a>
        </div>
    </div>
</section>



<section>
    <div class="container px-5 py-5">
        <div class="row">
            <div class="col-lg-12 text-center mb-5">
                <h2 class="display-4">Nos catégories de services</h2>
                <p class="lead">Explorez les différents domaines d'expertise</p>
            </div>
        </div>
        <div class="row gx-4">
            <?php foreach ($categories as $categorie): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-<?php echo htmlspecialchars($categorie['icone'] ?? 'star'); ?> fa-3x text-primary mb-3"></i>
                            <h5 class="card-title"><?php echo htmlspecialchars($categorie['nom']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($categorie['description'] ?? ''); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if (!empty($services)): ?>
<section class="bg-light">
    <div class="container px-5 py-5">
        <div class="row">
            <div class="col-lg-12 text-center mb-5">
                <h2 class="display-4">Services récents</h2>
                <p class="lead">Découvrez les dernières offres de services</p>
            </div>
        </div>
        <div class="row gx-4">
            <?php foreach ($services as $service): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if ($service['image']): ?>
                            <img src="<?php echo htmlspecialchars($service['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($service['titre']); ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-briefcase fa-3x text-white"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($service['titre']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars(substr($service['description'], 0, 100)) . '...'; ?></p>
                            <p class="text-muted small mb-2">
                                <i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($service['categorie_nom'] ?? 'Non catégorisé'); ?>
                            </p>
                            <p class="text-muted small mb-3">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($service['freelance_prenom'] . ' ' . $service['freelance_nom']); ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 text-primary mb-0"><?php echo number_format($service['prix'], 2); ?> TND</span>
                                <a href="services.php?id=<?php echo $service['id']; ?>" class="btn btn-sm btn-primary">Voir détails</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="services.php" class="btn btn-primary btn-lg">Voir tous les services</a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
