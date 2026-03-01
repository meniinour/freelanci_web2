<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';

$pageTitle = 'Laisser un avis';
requireLogin();

$pdo = getDBConnection();
$commande_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = '';
$success = '';

$commande = null;
if ($commande_id) {
    $stmt = $pdo->prepare("SELECT c.*, s.titre as service_titre, u.nom as freelance_nom, u.prenom as freelance_prenom 
                           FROM commandes c 
                           JOIN services s ON c.service_id = s.id 
                           JOIN utilisateurs u ON c.freelance_id = u.id 
                           WHERE c.id = ? AND c.client_id = ? AND c.statut = 'terminee'");
    $stmt->execute([$commande_id, $_SESSION['user_id']]);
    $commande = $stmt->fetch();
}

if (!$commande) {
    $error = 'Commande introuvable ou vous ne pouvez pas noter cette commande.';
}

$deja_avis = false;
if ($commande) {
    $stmt = $pdo->prepare("SELECT id FROM avis WHERE commande_id = ?");
    $stmt->execute([$commande_id]);
    $deja_avis = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $commande && !$deja_avis) {
    $note = (int)($_POST['note'] ?? 0);
    $commentaire = trim($_POST['commentaire'] ?? '');
    if ($note < 1 || $note > 5) {
        $error = 'Veuillez choisir une note entre 1 et 5.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO avis (commande_id, client_id, freelance_id, note, commentaire) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$commande_id, $_SESSION['user_id'], $commande['freelance_id'], $note, $commentaire ?: null])) {
            $success = 'Merci pour votre avis !';
            $deja_avis = true;
        } else {
            $error = 'Erreur lors de l\'enregistrement.';
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<header class="masthead text-center text-white" style="padding-top: 100px; padding-bottom: 50px;">
    <div class="masthead-content">
        <div class="container px-5">
            <h1 class="masthead-heading mb-0"><?php echo $commande ? 'Laisser un avis' : 'Avis'; ?></h1>
        </div>
    </div>
</header>

<section style="padding: 80px 0;">
    <div class="container px-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <a href="profile.php" class="btn btn-primary">Retour au profil</a>
                <?php elseif ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <a href="profile.php" class="btn btn-primary">Retour au profil</a>
                <?php elseif ($deja_avis): ?>
                    <div class="alert alert-info">Vous avez déjà laissé un avis pour cette commande.</div>
                    <a href="profile.php" class="btn btn-primary">Retour au profil</a>
                <?php elseif ($commande): ?>
                    <div class="card shadow">
                        <div class="card-body">
                            <p><strong>Service :</strong> <?php echo htmlspecialchars($commande['service_titre']); ?></p>
                            <p><strong>Freelance :</strong> <?php echo htmlspecialchars($commande['freelance_prenom'] . ' ' . $commande['freelance_nom']); ?></p>
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Note (1 à 5 étoiles) *</label>
                                    <div class="d-flex gap-2">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <label class="form-check-label"><input type="radio" name="note" value="<?php echo $i; ?>" required> <?php echo $i; ?> ★</label>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="commentaire" class="form-label">Commentaire (optionnel)</label>
                                    <textarea class="form-control" id="commentaire" name="commentaire" rows="4"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Envoyer l'avis</button>
                                <a href="profile.php" class="btn btn-secondary">Annuler</a>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
