<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

$pageTitle = 'Back Office - Catégories';
requireAdmin();

$pdo = getDBConnection();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $icone = trim($_POST['icone'] ?? '');
    $categorie_id = !empty($_POST['categorie_id']) ? intval($_POST['categorie_id']) : null;
    
    if (empty($nom)) {
        $error = 'Le nom de la catégorie est obligatoire.';
    } else {
        if ($categorie_id) {
            $stmt = $pdo->prepare("UPDATE categories SET nom = ?, description = ?, icone = ? WHERE id = ?");
            if ($stmt->execute([$nom, $description, $icone, $categorie_id])) {
                $success = 'Catégorie modifiée avec succès !';
            } else {
                $error = 'Erreur lors de la modification.';
            }
        } else {
            $stmt = $pdo->prepare("INSERT INTO categories (nom, description, icone) VALUES (?, ?, ?)");
            if ($stmt->execute([$nom, $description, $icone])) {
                $success = 'Catégorie ajoutée avec succès !';
            } else {
                $error = 'Erreur lors de l\'ajout.';
            }
        }
    }
}

if (isset($_GET['supprimer'])) {
    $categorie_id = intval($_GET['supprimer']);
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM services WHERE categorie_id = ?");
    $stmt->execute([$categorie_id]);
    $count = $stmt->fetch()['count'];
    
    if ($count > 0) {
        $error = 'Cette catégorie est utilisée par ' . $count . ' service(s). Impossible de la supprimer.';
    } else {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        if ($stmt->execute([$categorie_id])) {
            $success = 'Catégorie supprimée avec succès !';
        } else {
            $error = 'Erreur lors de la suppression.';
        }
    }
}

$stmt = $pdo->query("SELECT c.*, COUNT(s.id) as nb_services 
                     FROM categories c 
                     LEFT JOIN services s ON c.id = s.categorie_id 
                     GROUP BY c.id 
                     ORDER BY c.nom");
$categories = $stmt->fetchAll();
$categorie_edit = null;
if (isset($_GET['modifier'])) {
    $categorie_id = intval($_GET['modifier']);
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$categorie_id]);
    $categorie_edit = $stmt->fetch();
}

include __DIR__ . '/../includes/header.php';
?>

<header class="masthead text-center text-white" style="padding-top: 100px; padding-bottom: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="masthead-content">
        <div class="container px-5">
            <h1 class="masthead-heading mb-0">Gestion des catégories</h1>
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
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-<?php echo $categorie_edit ? 'edit' : 'plus'; ?> me-2"></i>
                    <?php echo $categorie_edit ? 'Modifier une catégorie' : 'Ajouter une nouvelle catégorie'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <?php if ($categorie_edit): ?>
                        <input type="hidden" name="categorie_id" value="<?php echo $categorie_edit['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom de la catégorie *</label>
                        <input type="text" class="form-control" id="nom" name="nom" 
                               value="<?php echo htmlspecialchars($categorie_edit['nom'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($categorie_edit['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="icone" class="form-label">Icône Font Awesome</label>
                        <input type="text" class="form-control" id="icone" name="icone" 
                               value="<?php echo htmlspecialchars($categorie_edit['icone'] ?? ''); ?>" 
                               placeholder="Ex: code, palette, pen">
                        <small class="form-text text-muted">Nom de l'icône Font Awesome (sans le préfixe "fa-")</small>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-info text-white">
                            <i class="fas fa-save me-2"></i><?php echo $categorie_edit ? 'Modifier' : 'Ajouter'; ?>
                        </button>
                        <?php if ($categorie_edit): ?>
                            <a href="categories.php" class="btn btn-secondary">Annuler</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card shadow-lg">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Liste des catégories (<?php echo count($categories); ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($categories)): ?>
                    <p class="text-center text-muted">Aucune catégorie trouvée.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Icône</th>
                                    <th>Services</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $categorie): ?>
                                    <tr>
                                        <td><?php echo $categorie['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($categorie['nom']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($categorie['description'] ?? ''); ?></td>
                                        <td>
                                            <?php if ($categorie['icone']): ?>
                                                <i class="fas fa-<?php echo htmlspecialchars($categorie['icone']); ?>"></i>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?php echo $categorie['nb_services']; ?></span>
                                        </td>
                                        <td>
                                            <a href="?modifier=<?php echo $categorie['id']; ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?supprimer=<?php echo $categorie['id']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');">
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
