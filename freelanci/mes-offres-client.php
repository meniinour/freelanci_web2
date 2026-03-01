<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';

$pageTitle = 'Mes offres';
Auth::requireLogin();
if (!Auth::isClient()) {
    header('Location: index.php');
    exit;
}

$user_id = Auth::getUserId();
$tables_ok = OffreClient::tableExists();
$offres = $tables_ok ? OffreClient::getByClient($user_id) : [];

if (isset($_GET['supprimer_offre']) && $tables_ok) {
    $offre_id = (int)$_GET['supprimer_offre'];
    if (OffreClient::delete($offre_id, $user_id)) {
        header('Location: mes-offres-client.php?supprime=1');
        exit;
    }
}

include __DIR__ . '/includes/header.php';
?>

<header class="masthead text-center text-white" style="padding-top: 100px; padding-bottom: 50px;">
    <div class="masthead-content">
        <div class="container px-5">
            <h1 class="masthead-heading mb-0">Mes offres</h1>
            <h2 class="masthead-subheading mb-0">Voir qui participe à vos offres</h2>
        </div>
    </div>
</header>

<section style="padding: 80px 0;">
    <div class="container px-5">
        <?php if (isset($_GET['ok'])): ?>
            <div class="alert alert-success">Offre déposée. Les freelances peuvent y participer.</div>
        <?php endif; ?>

        <?php if (!$tables_ok): ?>
            <div class="alert alert-warning">Exécutez <a href="install_migration.php">install_migration.php</a> pour activer les offres clients.</div>
        <?php else: ?>
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h4 class="mb-0">Offres déposées (<?php echo count($offres); ?>)</h4>
                <a href="deposer-offre.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Déposer une offre</a>
            </div>

            <?php if (empty($offres)): ?>
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-bullhorn fa-4x text-muted mb-3"></i>
                        <p class="lead">Vous n'avez pas encore déposé d'offre.</p>
                        <p class="text-muted">Déposez une offre pour que les freelances puissent participer.</p>
                        <a href="deposer-offre.php" class="btn btn-primary btn-lg">Déposer ma première offre</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($offres as $o): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($o['titre']); ?></h5>
                                    <p class="card-text small text-muted"><?php echo htmlspecialchars(substr($o['description'], 0, 120)); ?>...</p>
                                    <?php if ($o['budget_max']): ?>
                                        <p class="small mb-2"><strong>Budget max :</strong> <?php echo number_format($o['budget_max'], 2); ?> TND</p>
                                    <?php endif; ?>
                                    <p class="small text-muted mb-2"><?php echo date('d/m/Y H:i', strtotime($o['date_creation'])); ?></p>
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <span class="badge bg-<?php echo $o['statut'] === 'ouverte' ? 'success' : 'secondary'; ?>"><?php echo $o['statut']; ?></span>
                                        <div class="d-flex gap-1">
                                            <a href="participants-offre.php?id=<?php echo $o['id']; ?>" class="btn btn-primary btn-sm">
                                                <i class="fas fa-users me-1"></i>Voir participants (<?php echo (int)$o['nb_participations']; ?>)
                                            </a>
                                            <a href="mes-offres-client.php?supprimer_offre=<?php echo $o['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette offre ? Les participations seront aussi supprimées.');" title="Supprimer l\'offre">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
