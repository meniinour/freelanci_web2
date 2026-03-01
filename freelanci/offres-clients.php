<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';
$pageTitle = 'Offres des clients';
Auth::requireLogin();
if (!Auth::isFreelance()) {
    header('Location: index.php');
    exit;
}
$user_id = Auth::getUserId();
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['participer'])) {
    $offre_id = (int)($_POST['offre_id'] ?? 0);
    $message = trim($_POST['message'] ?? '');
    if ($offre_id && $message !== '') {
        if (ParticipationOffre::create($offre_id, $user_id, $message)) {
            $success = 'Votre participation a été envoyée au client.';
        } else {
            $error = 'Vous avez déjà participé à cette offre ou erreur.';
        }
    } else {
        $error = 'Message obligatoire.';
    }
}

$tables_ok = OffreClient::tableExists();
$offres = [];
if ($tables_ok) {
    $offres = OffreClient::getOuvertes();
    foreach ($offres as &$of) {
        $of['deja_participe'] = ParticipationOffre::hasParticipated((int)$of['id'], $user_id);
    }
    unset($of);
}

include __DIR__ . '/includes/header.php';
?>
<header class="masthead text-center text-white" style="padding-top: 100px; padding-bottom: 50px;">
    <div class="masthead-content"><div class="container px-5">
        <h1 class="masthead-heading mb-0">Offres des clients</h1>
        <h2 class="masthead-subheading mb-0">Participez aux projets publiés par les clients</h2>
    </div></div>
</header>
<section style="padding: 80px 0;">
    <div class="container px-5">
        <?php if (!empty($success)): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
        <?php if (!empty($error)): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <?php if (!$tables_ok): ?>
            <div class="alert alert-warning">Fonctionnalité en cours d'activation (migration).</div>
        <?php elseif (empty($offres)): ?>
            <div class="card shadow"><div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <p class="lead">Aucune offre ouverte pour le moment.</p>
                <a href="mes-offres.php" class="btn btn-primary">Mes offres (commandes)</a>
            </div></div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($offres as $o): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($o['titre']); ?></h5>
                                <p class="small text-muted">Par <?php echo htmlspecialchars($o['client_prenom'] . ' ' . $o['client_nom']); ?></p>
                                <p class="card-text small"><?php echo htmlspecialchars(substr($o['description'], 0, 150)); ?>...</p>
                                <?php if ($o['budget_max']): ?><p class="small mb-2"><strong>Budget max :</strong> <?php echo number_format($o['budget_max'], 2); ?> TND</p><?php endif; ?>
                                <p class="small text-muted"><?php echo (int)$o['nb_participations']; ?> participation(s)</p>
                                <?php if (!empty($o['deja_participe'])): ?>
                                    <span class="badge bg-success">Vous avez participé</span>
                                <?php else: ?>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalParticiper<?php echo $o['id']; ?>"><i class="fas fa-paper-plane me-1"></i>Participer</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="modalParticiper<?php echo $o['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Participer : <?php echo htmlspecialchars($o['titre']); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="offre_id" value="<?php echo $o['id']; ?>">
                                        <input type="hidden" name="participer" value="1">
                                        <label class="form-label">Votre message au client *</label>
                                        <textarea class="form-control" name="message" rows="4" required placeholder="Présentez-vous et expliquez pourquoi vous êtes le bon freelance..."></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Envoyer ma participation</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-4"><a href="mes-participations.php" class="btn btn-outline-primary">Voir mes participations</a></div>
        <?php endif; ?>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
