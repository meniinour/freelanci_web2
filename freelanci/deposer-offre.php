<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';

$pageTitle = 'Déposer une offre';
Auth::requireLogin();
if (!Auth::isClient()) {
    header('Location: index.php');
    exit;
}

$user_id = Auth::getUserId();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $budget_max = isset($_POST['budget_max']) && $_POST['budget_max'] !== '' ? (float)$_POST['budget_max'] : null;
    if (empty($titre) || empty($description)) {
        $error = 'Titre et description obligatoires.';
    } elseif (OffreClient::create($user_id, $titre, $description, $budget_max)) {
        header('Location: mes-offres-client.php?ok=1');
        exit;
    } else {
        $error = 'Erreur lors de l\'enregistrement.';
    }
}

include __DIR__ . '/includes/header.php';
?>

<header class="masthead text-center text-white" style="padding-top: 100px; padding-bottom: 50px;">
    <div class="masthead-content">
        <div class="container px-5">
            <h1 class="masthead-heading mb-0">Déposer une offre</h1>
            <h2 class="masthead-subheading mb-0">Publiez votre projet, les freelances participeront</h2>
        </div>
    </div>
</header>

<section style="padding: 80px 0;">
    <div class="container px-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <div class="card shadow-lg">
                    <div class="card-body p-4">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="titre" class="form-label">Titre de l'offre *</label>
                                <input type="text" class="form-control" id="titre" name="titre" required
                                       value="<?php echo htmlspecialchars($_POST['titre'] ?? ''); ?>"
                                       placeholder="Ex: Création d'un logo pour ma marque">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description du projet *</label>
                                <textarea class="form-control" id="description" name="description" rows="5" required
                                          placeholder="Décrivez votre besoin, délais, attentes..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="budget_max" class="form-label">Budget max (TND, optionnel)</label>
                                <input type="number" class="form-control" id="budget_max" name="budget_max" step="0.01" min="0"
                                       value="<?php echo htmlspecialchars($_POST['budget_max'] ?? ''); ?>"
                                       placeholder="Ex: 500">
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-paper-plane me-2"></i>Publier mon offre</button>
                            <a href="mes-offres-client.php" class="btn btn-secondary btn-lg">Mes offres</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
