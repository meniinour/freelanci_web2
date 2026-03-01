<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

$pageTitle = 'Back Office - Services';
requireAdmin();

$pdo = getDBConnection();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $prix = floatval($_POST['prix'] ?? 0);
    $categorie_id = !empty($_POST['categorie_id']) ? intval($_POST['categorie_id']) : null;
    $freelance_id = intval($_POST['freelance_id'] ?? 0);
    $statut = $_POST['statut'] ?? 'actif';
    $service_id = !empty($_POST['service_id']) ? intval($_POST['service_id']) : null;
    
    if (empty($titre) || empty($description) || $prix <= 0 || $freelance_id <= 0) {
        $error = 'Veuillez remplir tous les champs correctement.';
    } else {
        if ($service_id) {
            $stmt = $pdo->prepare("UPDATE services SET titre = ?, description = ?, prix = ?, categorie_id = ?, freelance_id = ?, statut = ? WHERE id = ?");
            if ($stmt->execute([$titre, $description, $prix, $categorie_id, $freelance_id, $statut, $service_id])) {
                $success = 'Service modifié avec succès !';
            } else {
                $error = 'Erreur lors de la modification.';
            }
        } else {
            $stmt = $pdo->prepare("INSERT INTO services (titre, description, prix, categorie_id, freelance_id, statut) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$titre, $description, $prix, $categorie_id, $freelance_id, $statut])) {
                $success = 'Service ajouté avec succès !';
            } else {
                $error = 'Erreur lors de l\'ajout.';
            }
        }
    }
}

if (isset($_GET['supprimer'])) {
    $service_id = intval($_GET['supprimer']);
    $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
    if ($stmt->execute([$service_id])) {
        $success = 'Service supprimé avec succès !';
    } else {
        $error = 'Erreur lors de la suppression.';
    }
}

$stmt = $pdo->query("SELECT s.*, u.nom as freelance_nom, u.prenom as freelance_prenom, c.nom as categorie_nom 
                     FROM services s 
                     JOIN utilisateurs u ON s.freelance_id = u.id 
                     LEFT JOIN categories c ON s.categorie_id = c.id 
                     ORDER BY s.date_creation DESC");
$services = $stmt->fetchAll();
$stmt = $pdo->query("SELECT id, nom, prenom FROM utilisateurs WHERE type_utilisateur = 'freelance' ORDER BY nom");
$freelances = $stmt->fetchAll();
$stmt = $pdo->query("SELECT * FROM categories ORDER BY nom");
$categories = $stmt->fetchAll();
$service_edit = null;
if (isset($_GET['modifier'])) {
    $service_id = intval($_GET['modifier']);
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$service_id]);
    $service_edit = $stmt->fetch();
}

include __DIR__ . '/../includes/header.php';
?>

<header class="masthead text-center text-white" style="padding-top: 100px; padding-bottom: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="masthead-content">
        <div class="container px-5">
            <h1 class="masthead-heading mb-0">Gestion des services</h1>
            <h2 class="masthead-subheading mb-0">Back Office - Administration</h2>
        </div>
    </div>
</header>

<section style="padding: 80px 0;">
    <div class="container px-5">
        
        <div class="mb-4">
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour au dashboard
            </a>
        </div>
        
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
        
        <div class="card shadow-lg mb-4">
            <div class="card-header bg-success text-white">
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
                            <label for="prix" class="form-label">Prix (TND) *</label>
                            <input type="number" class="form-control" id="prix" name="prix" 
                                   step="0.01" min="0" 
                                   value="<?php echo $service_edit['prix'] ?? ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="freelance_id" class="form-label">Freelance *</label>
                            <select class="form-select" id="freelance_id" name="freelance_id" required>
                                <option value="">Sélectionner un freelance</option>
                                <?php foreach ($freelances as $freelance): ?>
                                    <option value="<?php echo $freelance['id']; ?>" 
                                            <?php echo ($service_edit && $service_edit['freelance_id'] == $freelance['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($freelance['prenom'] . ' ' . $freelance['nom']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
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
                    
                    <div class="mb-3">
                        <label for="statut" class="form-label">Statut *</label>
                        <select class="form-select" id="statut" name="statut" required>
                            <option value="actif" <?php echo (!$service_edit || $service_edit['statut'] === 'actif') ? 'selected' : ''; ?>>Actif</option>
                            <option value="inactif" <?php echo ($service_edit && $service_edit['statut'] === 'inactif') ? 'selected' : ''; ?>>Inactif</option>
                        </select>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i><?php echo $service_edit ? 'Modifier' : 'Ajouter'; ?>
                        </button>
                        <?php if ($service_edit): ?>
                            <a href="services.php" class="btn btn-secondary">Annuler</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card shadow-lg">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Liste des services (<?php echo count($services); ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($services)): ?>
                    <p class="text-center text-muted">Aucun service trouvé.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Titre</th>
                                    <th>Freelance</th>
                                    <th>Catégorie</th>
                                    <th>Prix</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($services as $service): ?>
                                    <tr>
                                        <td><?php echo $service['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($service['titre']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($service['freelance_prenom'] . ' ' . $service['freelance_nom']); ?></td>
                                        <td><?php echo htmlspecialchars($service['categorie_nom'] ?? 'Non catégorisé'); ?></td>
                                        <td><?php echo number_format($service['prix'], 2); ?> TND</td>
                                        <td>
                                            <span class="badge bg-<?php echo $service['statut'] === 'actif' ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($service['statut']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($service['date_creation'])); ?></td>
                                        <td>
                                            <a href="?modifier=<?php echo $service['id']; ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?supprimer=<?php echo $service['id']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce service ?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
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

<?php include __DIR__ . '/../includes/footer.php'; ?>
