<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';

$pageTitle = 'Mes participations';
Auth::requireLogin();
if (!Auth::isFreelance()) {
    header('Location: index.php');
    exit;
}
$user_id = Auth::getUserId();
$tables_ok = ParticipationOffre::tableExists();
$participations = $tables_ok ? ParticipationOffre::getByFreelance($user_id) : [];

include __DIR__ . '/includes/header.php';
?>

<header class="masthead text-center text-white" style="padding-top: 100px; padding-bottom: 50px;">
    <div class="masthead-content">
        <div class="container px-5">
            <h1 class="masthead-heading mb-0">Mes participations</h1>
            <h2 class="masthead-subheading mb-0">Offres auxquelles vous avez participé</h2>
        </div>
    </div>
</header>

<section style="padding: 80px 0;">
    <div class="container px-5">
        <?php if (!$tables_ok): ?>
            <div class="alert alert-warning">Fonctionnalité en cours d'activation.</div>
        <?php elseif (empty($participations)): ?>
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <p class="lead text-muted">Aucune participation pour le moment.</p>
                    <a href="offres-clients.php" class="btn btn-primary">Voir les offres des clients</a>
                </div>
            </div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($participations as $p): ?>
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1"><?php echo htmlspecialchars($p['offre_titre']); ?></h6>
                            <small><?php echo date('d/m/Y H:i', strtotime($p['date_participation'])); ?></small>
                        </div>
                        <p class="mb-1 small text-muted">Client : <?php echo htmlspecialchars($p['client_prenom'] . ' ' . $p['client_nom']); ?></p>
                        <p class="mb-1 small"><?php echo nl2br(htmlspecialchars(substr($p['message'], 0, 200))); ?><?php echo strlen($p['message']) > 200 ? '...' : ''; ?></p>
                        <span class="badge bg-<?php echo $p['offre_statut'] === 'ouverte' ? 'success' : 'secondary'; ?>"><?php echo $p['offre_statut']; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-3">
                <a href="offres-clients.php" class="btn btn-primary">Voir les offres des clients</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
