<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

$pageTitle = 'Back Office - Utilisateurs';
requireAdmin();

$pdo = getDBConnection();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
    $user_id = intval($_POST['user_id']);
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $type_utilisateur = $_POST['type_utilisateur'] ?? 'client';
    $statut = $_POST['statut'] ?? 'actif';
    
    if (empty($nom) || empty($prenom) || empty($email)) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user_id]);
        if ($stmt->fetch()) {
            $error = 'Cet email est déjà utilisé.';
        } else {
            $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, type_utilisateur = ?, statut = ? WHERE id = ?");
            if ($stmt->execute([$nom, $prenom, $email, $type_utilisateur, $statut, $user_id])) {
                $success = 'Utilisateur modifié avec succès !';
            } else {
                $error = 'Erreur lors de la modification.';
            }
        }
    }
}

if (isset($_GET['supprimer'])) {
    $user_id = intval($_GET['supprimer']);
 
    if ($user_id == $_SESSION['user_id']) {
        $error = 'Vous ne pouvez pas supprimer votre propre compte.';
    } else {
        $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ? AND type_utilisateur != 'admin'");
        if ($stmt->execute([$user_id])) {
            $success = 'Utilisateur supprimé avec succès !';
        } else {
            $error = 'Erreur lors de la suppression.';
        }
    }
}


$type_filter = $_GET['type'] ?? '';

$sql = "SELECT * FROM utilisateurs WHERE type_utilisateur != 'admin'";
$params = [];

if (!empty($type_filter)) {
    $sql .= " AND type_utilisateur = ?";
    $params[] = $type_filter;
}

$sql .= " ORDER BY date_inscription DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$utilisateurs = $stmt->fetchAll();
$user_edit = null;
if (isset($_GET['modifier'])) {
    $user_id = intval($_GET['modifier']);
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_edit = $stmt->fetch();
}

include __DIR__ . '/../includes/header.php';
?>

<header class="masthead text-center text-white" style="padding-top: 100px; padding-bottom: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="masthead-content">
        <div class="container px-5">
            <h1 class="masthead-heading mb-0">Gestion des utilisateurs</h1>
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
        
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="" class="row g-3">
                    <div class="col-md-4">
                        <label for="type" class="form-label">Filtrer par type</label>
                        <select class="form-select" id="type" name="type" onchange="this.form.submit()">
                            <option value="">Tous les types</option>
                            <option value="freelance" <?php echo ($type_filter === 'freelance') ? 'selected' : ''; ?>>Freelances</option>
                            <option value="client" <?php echo ($type_filter === 'client') ? 'selected' : ''; ?>>Clients</option>
                        </select>
                    </div>
                    <div class="col-md-8 d-flex align-items-end">
                        <a href="utilisateurs.php" class="btn btn-secondary">Réinitialiser</a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Liste des utilisateurs (<?php echo count($utilisateurs); ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($utilisateurs)): ?>
                    <p class="text-center text-muted">Aucun utilisateur trouvé.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th>Statut</th>
                                    <th>Date inscription</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($utilisateurs as $user): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($user['prenom']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['type_utilisateur'] === 'freelance' ? 'success' : 'info'; ?>">
                                                <?php echo ucfirst($user['type_utilisateur']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['statut'] === 'actif' ? 'success' : 'danger'; ?>">
                                                <?php echo ucfirst($user['statut']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($user['date_inscription'])); ?></td>
                                        <td>
                                            <a href="?modifier=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <a href="?supprimer=<?php echo $user['id']; ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($user_edit): ?>
            <div class="card shadow-lg mt-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Modifier l'utilisateur</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="user_id" value="<?php echo $user_edit['id']; ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">Nom *</label>
                                <input type="text" class="form-control" id="nom" name="nom" 
                                       value="<?php echo htmlspecialchars($user_edit['nom']); ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="prenom" class="form-label">Prénom *</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" 
                                       value="<?php echo htmlspecialchars($user_edit['prenom']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user_edit['email']); ?>" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type_utilisateur" class="form-label">Type d'utilisateur *</label>
                                <select class="form-select" id="type_utilisateur" name="type_utilisateur" required>
                                    <option value="client" <?php echo ($user_edit['type_utilisateur'] === 'client') ? 'selected' : ''; ?>>Client</option>
                                    <option value="freelance" <?php echo ($user_edit['type_utilisateur'] === 'freelance') ? 'selected' : ''; ?>>Freelance</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="statut" class="form-label">Statut *</label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="actif" <?php echo ($user_edit['statut'] === 'actif') ? 'selected' : ''; ?>>Actif</option>
                                    <option value="bloque" <?php echo ($user_edit['statut'] === 'bloque') ? 'selected' : ''; ?>>Bloqué</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" name="modifier" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>Enregistrer
                            </button>
                            <a href="utilisateurs.php" class="btn btn-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
