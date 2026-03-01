<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';

$pageTitle = 'Mes Services';
Auth::requireLogin();
if (!Auth::isFreelance()) {
    header('Location: index.php');
    exit;
}

$user_id = Auth::getUserId();
$error = '';
$success = '';
$services = Service::getByFreelance($user_id);
$categories = Categorie::getAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $prix = (float)($_POST['prix'] ?? 0);
    $delai_jours = max(1, min(90, (int)($_POST['delai_jours'] ?? 7)));
    $categorie_id = !empty($_POST['categorie_id']) ? (int)$_POST['categorie_id'] : null;
    $service_id = !empty($_POST['service_id']) ? (int)$_POST['service_id'] : null;
    if (empty($titre) || empty($description) || $prix <= 0) {
        $error = 'Veuillez remplir tous les champs correctement.';
    } else {
        if ($service_id) {
            if (Service::update($service_id, ['titre' => $titre, 'description' => $description, 'prix' => $prix, 'delai_jours' => $delai_jours, 'categorie_id' => $categorie_id])) {
                $success = 'Service modifié avec succès !';
                $services = Service::getByFreelance($user_id);
            } else {
                $error = 'Erreur lors de la modification.';
            }
        } else {
            if (Service::create(['titre' => $titre, 'description' => $description, 'prix' => $prix, 'delai_jours' => $delai_jours, 'categorie_id' => $categorie_id, 'freelance_id' => $user_id])) {
                $success = 'Service ajouté avec succès !';
                $services = Service::getByFreelance($user_id);
            } else {
                $error = 'Erreur lors de l\'ajout.';
            }
        }
    }
}

if (isset($_GET['supprimer'])) {
    $sid = (int)$_GET['supprimer'];
    if (Service::delete($sid, $user_id)) {
        $success = 'Service supprimé avec succès !';
        $services = Service::getByFreelance($user_id);
    } else {
        $error = 'Erreur lors de la suppression.';
    }
}

$service_edit = null;
if (isset($_GET['modifier'])) {
    $sid = (int)$_GET['modifier'];
    $row = Service::find($sid);
    if ($row && (int)$row['freelance_id'] === $user_id) {
        $service_edit = $row;
    }
}

include __DIR__ . '/includes/header.php';
?>

<!-- Header -->
<header class="masthead text-center text-white" style="padding-top: 100px; padding-bottom: 50px;">
    <div class="masthead-content">
        <div class="container px-5">
            <h1 class="masthead-heading mb-0">Mes Services</h1>
            <h2 class="masthead-subheading mb-0">Gérez vos offres de services</h2>
        </div>
    </div>
</header>

<!-- Section Services -->
<section style="padding: 80px 0;">
    <div class="container px-5">
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Formulaire d'ajout/modification -->
        <div class="card shadow-lg mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-<?php echo $service_edit ? 'edit' : 'plus'; ?> me-2"></i>
                    <?php echo $service_edit ? 'Modifier un service' : 'Ajouter un nouveau service'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <?php if ($service_edit): ?>
                        <input type="hidden" name="service_id" value="<?php echo $service_edit['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="titre" class="form-label">Titre du service *</label>
                            <input type="text" class="form-control" id="titre" name="titre" 
                                   value="<?php echo htmlspecialchars($service_edit['titre'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="categorie_id" class="form-label">Catégorie</label>
                            <select class="form-select" id="categorie_id" name="categorie_id">
                                <option value="">Sélectionner une catégorie</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" 
                                            <?php echo ($service_edit && $service_edit['categorie_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['nom']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($service_edit['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="prix" class="form-label">Prix (TND) *</label>
                            <input type="number" class="form-control" id="prix" name="prix" 
                                   step="0.01" min="0" 
                                   value="<?php echo $service_edit['prix'] ?? ''; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="delai_jours" class="form-label">Délai de livraison (jours)</label>
                            <select class="form-select" id="delai_jours" name="delai_jours">
                                <option value="1" <?php echo ($service_edit['delai_jours'] ?? 7) == 1 ? 'selected' : ''; ?>>1 jour</option>
                                <option value="3" <?php echo ($service_edit['delai_jours'] ?? 7) == 3 ? 'selected' : ''; ?>>3 jours</option>
                                <option value="7" <?php echo ($service_edit['delai_jours'] ?? 7) == 7 ? 'selected' : ''; ?>>7 jours</option>
                                <option value="14" <?php echo ($service_edit['delai_jours'] ?? 7) == 14 ? 'selected' : ''; ?>>14 jours</option>
                                <option value="30" <?php echo ($service_edit['delai_jours'] ?? 7) == 30 ? 'selected' : ''; ?>>30 jours</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i><?php echo $service_edit ? 'Modifier' : 'Ajouter'; ?>
                        </button>
                        <?php if ($service_edit): ?>
                            <a href="mes-services.php" class="btn btn-secondary">Annuler</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Liste des services -->
        <div class="card shadow-lg">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Mes services (<?php echo count($services); ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($services)): ?>
                    <p class="text-center text-muted">Vous n'avez pas encore de services. Ajoutez-en un ci-dessus !</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Catégorie</th>
                                    <th>Prix</th>
                                    <th>Délai</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($services as $service): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($service['titre']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($service['categorie_nom'] ?? 'Non catégorisé'); ?></td>
                                        <td><?php echo number_format($service['prix'], 2); ?> TND</td>
                                        <td><?php echo (int)($service['delai_jours'] ?? 7); ?> j</td>
                                        <td>
                                            <span class="badge bg-<?php echo $service['statut'] === 'actif' ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($service['statut']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($service['date_creation'])); ?></td>
                                        <td>
                                            <a href="packages.php?service_id=<?php echo $service['id']; ?>" class="btn btn-sm btn-info" title="Packages"><i class="fas fa-boxes"></i></a>
                                            <a href="?modifier=<?php echo $service['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                            <a href="?supprimer=<?php echo $service['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce service ?');"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
