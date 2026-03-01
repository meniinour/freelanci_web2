<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';

$pageTitle = 'Mes offres';
Auth::requireLogin();
if (!Auth::isFreelance()) {
    header('Location: index.php');
    exit;
}

$user_id = Auth::getUserId();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commande_action'])) {
    $cid = (int)($_POST['commande_id'] ?? 0);
    $action = $_POST['commande_action'] ?? '';
    if ($action === 'accepter') {
        if (Commande::updateStatut($cid, 'en_cours', $user_id)) $success = 'Offre acceptée.';
    } elseif ($action === 'refuser') {
        if (Commande::updateStatut($cid, 'refusee', $user_id)) $success = 'Offre refusée.';
    } elseif ($action === 'livrer') {
        $url = trim($_POST['livrable_url'] ?? '');
        if ($url !== '' && Commande::setLivrable($cid, $url, $user_id)) $success = 'Livrable enregistré.';
    }
}

$offres = Commande::getByFreelance($user_id);

include __DIR__ . '/includes/header.php';
?>

<header class="masthead text-center text-white" style="padding-top: 100px; padding-bottom: 50px;">
    <div class="masthead-content">
        <div class="container px-5">
            <h1 class="masthead-heading mb-0">Mes offres</h1>
            <h2 class="masthead-subheading mb-0">Consulter les demandes reçues des clients</h2>
        </div>
    </div>
</header>

<section style="padding: 80px 0;">
    <div class="container px-5">
        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Offres reçues (<?php echo count($offres); ?>)</h5>
                <a href="profile.php" class="btn btn-sm btn-light text-dark">Mon profil</a>
            </div>
            <div class="card-body">
                <?php if (empty($offres)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <p class="lead text-muted">Aucune offre pour le moment.</p>
                        <p class="text-muted">Les demandes des clients apparaîtront ici lorsqu'ils commanderont vos services.</p>
                        <a href="mes-services.php" class="btn btn-primary mt-2">Publier mes services</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Service</th>
                                    <th>Client</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($offres as $o): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($o['service_titre']); ?></strong>
                                            <br><span class="text-muted small"><?php echo number_format($o['service_prix'], 2); ?> TND</span>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($o['client_prenom'] . ' ' . $o['client_nom']); ?>
                                            <br><span class="text-muted small"><?php echo htmlspecialchars($o['client_email']); ?></span>
                                        </td>
                                        <td class="small"><?php echo nl2br(htmlspecialchars($o['message'] ?: '—')); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($o['date_commande'])); ?></td>
                                        <td>
                                            <?php
                                            $badge = ['en_attente' => 'warning', 'acceptee' => 'info', 'en_cours' => 'primary', 'refusee' => 'danger', 'livree' => 'info', 'terminee' => 'success'];
                                            $lib = ['en_attente' => 'En attente', 'acceptee' => 'Acceptée', 'en_cours' => 'En cours', 'refusee' => 'Refusée', 'livree' => 'Livrée', 'terminee' => 'Terminée'];
                                            $c = $badge[$o['statut']] ?? 'secondary';
                                            $l = $lib[$o['statut']] ?? $o['statut'];
                                            ?>
                                            <span class="badge bg-<?php echo $c; ?>"><?php echo $l; ?></span>
                                        </td>
                                        <td>
                                            <?php if ($o['statut'] === 'en_attente'): ?>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="commande_id" value="<?php echo $o['id']; ?>">
                                                    <input type="hidden" name="commande_action" value="accepter">
                                                    <button type="submit" class="btn btn-sm btn-success">Accepter</button>
                                                </form>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="commande_id" value="<?php echo $o['id']; ?>">
                                                    <input type="hidden" name="commande_action" value="refuser">
                                                    <button type="submit" class="btn btn-sm btn-danger">Refuser</button>
                                                </form>
                                            <?php endif; ?>
                                            <?php if (in_array($o['statut'], ['en_cours', 'acceptee'])): ?>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="commande_id" value="<?php echo $o['id']; ?>">
                                                    <input type="hidden" name="commande_action" value="livrer">
                                                    <input type="url" name="livrable_url" class="form-control form-control-sm d-inline-block" style="width:180px" placeholder="URL du livrable" required>
                                                    <button type="submit" class="btn btn-sm btn-primary">Marquer livrée</button>
                                                </form>
                                            <?php endif; ?>
                                            <a href="messages.php?contact=<?php echo (int)$o['client_id']; ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-envelope me-1"></i>Messages</a>
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
