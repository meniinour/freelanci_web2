<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';

$pageTitle = 'Participants à l\'offre';
Auth::requireLogin();
if (!Auth::isClient()) {
    header('Location: index.php');
    exit;
}

$user_id = Auth::getUserId();
$offre_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$offre = null;
if ($offre_id) {
    $row = OffreClient::find($offre_id);
    if ($row && (int)$row['client_id'] === $user_id) {
        $offre = $row;
    }
}
$participants = $offre ? ParticipationOffre::getByOffre($offre_id) : [];

include __DIR__ . '/includes/header.php';
?>

<header class="masthead text-center text-white" style="padding-top: 100px; padding-bottom: 50px;">
    <div class="masthead-content">
        <div class="container px-5">
            <h1 class="masthead-heading mb-0">Qui participe à cette offre</h1>
            <h2 class="masthead-subheading mb-0"><?php echo $offre ? htmlspecialchars($offre['titre']) : 'Offre'; ?></h2>
        </div>
    </div>
</header>

<section style="padding: 80px 0;">
    <div class="container px-5">
        <?php if (!$offre): ?>
            <div class="alert alert-warning">Offre introuvable.</div>
            <a href="mes-offres-client.php" class="btn btn-primary">Retour à mes offres</a>
        <?php else: ?>
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h5><?php echo htmlspecialchars($offre['titre']); ?></h5>
                    <p class="text-muted mb-0"><?php echo nl2br(htmlspecialchars($offre['description'])); ?></p>
                    <?php if ($offre['budget_max']): ?>
                        <p class="mb-0 mt-2"><strong>Budget max :</strong> <?php echo number_format($offre['budget_max'], 2); ?> TND</p>
                    <?php endif; ?>
                </div>
            </div>

            <h4 class="mb-3">Participants (<?php echo count($participants); ?>)</h4>
            <?php if (empty($participants)): ?>
                <div class="alert alert-info">Aucun freelance n'a encore participé à cette offre.</div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($participants as $p): ?>
                        <div class="col-md-6">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h6 class="card-title"><?php echo htmlspecialchars($p['prenom'] . ' ' . $p['nom']); ?></h6>
                                    <p class="small text-muted mb-1"><?php echo htmlspecialchars($p['email']); ?></p>
                                    <?php if (!empty($p['competences'])): ?>
                                        <p class="small mb-2"><strong>Compétences :</strong> <?php echo htmlspecialchars($p['competences']); ?></p>
                                    <?php endif; ?>
                                    <div class="border-top pt-2 mt-2">
                                        <strong>Message :</strong>
                                        <p class="small mb-0"><?php echo nl2br(htmlspecialchars($p['message'])); ?></p>
                                    </div>
                                    <p class="small text-muted mt-2 mb-0"><?php echo date('d/m/Y H:i', strtotime($p['date_participation'])); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="mt-4">
                <a href="mes-offres-client.php" class="btn btn-secondary">Retour à mes offres</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
