<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';

$pageTitle = 'Services';

$categorie_id = $_GET['categorie'] ?? '';
$search = $_GET['search'] ?? '';
$prix_min = isset($_GET['prix_min']) ? (float)$_GET['prix_min'] : null;
$prix_max = isset($_GET['prix_max']) ? (float)$_GET['prix_max'] : null;
$delai_max = isset($_GET['delai_max']) ? (int)$_GET['delai_max'] : null;
$tri = $_GET['tri'] ?? 'date';

$filters = [
    'categorie_id' => $categorie_id ?: null,
    'search' => $search ?: null,
    'prix_min' => $prix_min,
    'prix_max' => $prix_max,
    'delai_max' => $delai_max,
    'tri' => $tri,
];
$services = Service::getActifs($filters);
$categories = Categorie::getAll();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commander'])) {
    if (!Auth::isClient() && !Auth::isFreelance()) {
        $message = 'Vous devez être connecté pour commander un service.';
        $messageType = 'danger';
    } else {
        $service_id = (int)$_POST['service_id'];
        $client_id = Auth::getUserId();
        $message_commande = trim($_POST['message'] ?? '');
        $freelance_id = Service::getFreelanceId($service_id);
        if ($freelance_id) {
            if ($freelance_id == $client_id) {
                $message = 'Vous ne pouvez pas commander votre propre service.';
                $messageType = 'warning';
            } elseif (Commande::create($service_id, $client_id, $freelance_id, $message_commande)) {
                $message = 'Votre demande a été envoyée avec succès !';
                $messageType = 'success';
            } else {
                $message = 'Erreur lors de l\'envoi de la demande.';
                $messageType = 'danger';
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<!-- Header -->
<header class="masthead text-center text-white" style="padding-top: 100px; padding-bottom: 50px;">
    <div class="masthead-content">
        <div class="container px-5">
            <h1 class="masthead-heading mb-0">Services disponibles</h1>
            <h2 class="masthead-subheading mb-0">Trouvez le service parfait pour vos besoins</h2>
        </div>
    </div>
</header>

<!-- Section Services -->
<section style="padding: 80px 0;">
    <div class="container px-5">
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Filtres -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET" action="" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Rechercher</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="Titre ou description...">
                    </div>
                    <div class="col-md-2">
                        <label for="categorie" class="form-label">Catégorie</label>
                        <select class="form-select" id="categorie" name="categorie">
                            <option value="">Toutes</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($categorie_id == $cat['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['nom']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="prix_min" class="form-label">Prix min (TND)</label>
                        <input type="number" class="form-control" id="prix_min" name="prix_min" step="1" min="0" value="<?php echo $prix_min !== null ? (int)$prix_min : ''; ?>" placeholder="Min">
                    </div>
                    <div class="col-md-2">
                        <label for="prix_max" class="form-label">Prix max (TND)</label>
                        <input type="number" class="form-control" id="prix_max" name="prix_max" step="1" min="0" value="<?php echo $prix_max !== null ? (int)$prix_max : ''; ?>" placeholder="Max">
                    </div>
                    <div class="col-md-2">
                        <label for="delai_max" class="form-label">Délai max (jours)</label>
                        <select class="form-select" id="delai_max" name="delai_max">
                            <option value="">—</option>
                            <option value="3" <?php echo $delai_max === 3 ? 'selected' : ''; ?>>3 j</option>
                            <option value="7" <?php echo $delai_max === 7 ? 'selected' : ''; ?>>7 j</option>
                            <option value="14" <?php echo $delai_max === 14 ? 'selected' : ''; ?>>14 j</option>
                            <option value="30" <?php echo $delai_max === 30 ? 'selected' : ''; ?>>30 j</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="tri" class="form-label">Tri</label>
                        <select class="form-select" id="tri" name="tri">
                            <option value="date" <?php echo $tri === 'date' ? 'selected' : ''; ?>>Récents</option>
                            <option value="prix_asc" <?php echo $tri === 'prix_asc' ? 'selected' : ''; ?>>Prix ↑</option>
                            <option value="prix_desc" <?php echo $tri === 'prix_desc' ? 'selected' : ''; ?>>Prix ↓</option>
                            <option value="delai" <?php echo $tri === 'delai' ? 'selected' : ''; ?>>Délai</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search me-2"></i>Filtrer</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Liste des services -->
        <?php if (empty($services)): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Aucun service pour le moment.</strong>
                <p class="mb-2 mt-2">Pour tester avec des exemples (freelances + 8 services) : exécutez une fois <a href="init_demo.php" class="alert-link">init_demo.php</a> puis rechargez cette page. Mot de passe démo : <kbd>demo123</kbd></p>
                <?php if (isLoggedIn() && isFreelance()): ?>
                    <a href="mes-services.php" class="btn btn-primary btn-sm">Publier mon premier service</a>
                <?php elseif (!isLoggedIn()): ?>
                    <a href="register.php" class="btn btn-outline-primary btn-sm">Inscription</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($services as $service): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            <?php if ($service['image']): ?>
                                <img src="<?php echo htmlspecialchars($service['image']); ?>" 
                                     class="card-img-top" alt="<?php echo htmlspecialchars($service['titre']); ?>" 
                                     style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="fas fa-briefcase fa-3x text-white"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($service['titre']); ?></h5>
                                <p class="card-text flex-grow-1">
                                    <?php echo htmlspecialchars(substr($service['description'], 0, 150)); ?>
                                    <?php echo strlen($service['description']) > 150 ? '...' : ''; ?>
                                </p>
                                
                                <div class="mb-2">
                                    <span class="badge bg-primary">
                                        <i class="fas fa-tag me-1"></i>
                                        <?php echo htmlspecialchars($service['categorie_nom'] ?? 'Non catégorisé'); ?>
                                    </span>
                                </div>
                                
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-user me-1"></i>
                                    <?php echo htmlspecialchars($service['freelance_prenom'] . ' ' . $service['freelance_nom']); ?>
                                    <span class="ms-2"><i class="fas fa-clock me-1"></i><?php echo (int)($service['delai_jours'] ?? 7); ?> jours</span>
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <span class="h4 text-primary mb-0">
                                        <?php echo number_format($service['prix'], 2); ?> TND
                                    </span>
                                    <?php if (isLoggedIn() && (isClient() || (isFreelance() && $service['freelance_id'] != $_SESSION['user_id']))): ?>
                                        <button type="button" class="btn btn-primary btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#commandeModal<?php echo $service['id']; ?>">
                                            <i class="fas fa-shopping-cart me-1"></i>Commander
                                        </button>
                                    <?php elseif (!isLoggedIn()): ?>
                                        <a href="login.php?redirect=<?php echo urlencode('services.php' . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '')); ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-sign-in-alt me-1"></i>Connexion pour commander
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Modal de commande -->
                        <div class="modal fade" id="commandeModal<?php echo $service['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Commander : <?php echo htmlspecialchars($service['titre']); ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="">
                                        <div class="modal-body">
                                            <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                                            <p><strong>Prix :</strong> <?php echo number_format($service['prix'], 2); ?> TND</p>
                                            <p><strong>Livraison :</strong> <?php echo (int)($service['delai_jours'] ?? 7); ?> jours</p>
                                            <p><strong>Freelance :</strong> <?php echo htmlspecialchars($service['freelance_prenom'] . ' ' . $service['freelance_nom']); ?></p>
                                            <div class="mb-3">
                                                <label for="message<?php echo $service['id']; ?>" class="form-label">Votre message</label>
                                                <textarea class="form-control" id="message<?php echo $service['id']; ?>" 
                                                          name="message" rows="4" 
                                                          placeholder="Décrivez votre besoin..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" name="commander" class="btn btn-primary">Envoyer la demande</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
