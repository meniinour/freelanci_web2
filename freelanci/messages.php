<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';

$pageTitle = 'Messages';
Auth::requireLogin();

$user_id = Auth::getUserId();
$contact_id = isset($_GET['contact']) ? (int)$_GET['contact'] : 0;

$contacts = Commande::getContactsForUser($user_id);
$autre = null;
$commande_id_for_send = null;
if ($contact_id) {
    foreach ($contacts as $c) {
        if ((int)$c['id'] === $contact_id) {
            $autre = ['id' => $c['id'], 'prenom' => $c['prenom'], 'nom' => $c['nom']];
            break;
        }
    }
    if ($autre) {
        $commande_id_for_send = Commande::getFirstCommandeIdForContact($user_id, $contact_id);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $autre && isset($_POST['contenu']) && $commande_id_for_send) {
    $contenu = trim($_POST['contenu'] ?? '');
    if ($contenu !== '') {
        Message::send($commande_id_for_send, $user_id, $contact_id, $contenu);
    }
}

if ($contact_id) {
    Message::markAsReadForUserAndContact($user_id, $contact_id);
}

$messages = $autre ? Message::getByUserAndContact($user_id, $contact_id) : [];

include __DIR__ . '/includes/header.php';
?>

<header class="masthead text-center text-white" style="padding-top: 100px; padding-bottom: 50px;">
    <div class="masthead-content">
        <div class="container px-5">
            <h1 class="masthead-heading mb-0">Messages</h1>
            <h2 class="masthead-subheading mb-0"><?php echo $autre ? htmlspecialchars($autre['prenom'] . ' ' . $autre['nom']) : 'Toutes vos conversations'; ?></h2>
        </div>
    </div>
</header>

<section style="padding: 80px 0;">
    <div class="container px-5">
        <div class="row">
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="list-group">
                    <?php if (empty($contacts)): ?>
                        <p class="text-muted small mb-0">Aucun contact pour le moment.</p>
                    <?php else: ?>
                        <?php foreach ($contacts as $c): ?>
                            <a href="messages.php?contact=<?php echo $c['id']; ?>" class="list-group-item list-group-item-action <?php echo $contact_id && (int)$c['id'] === $contact_id ? 'active' : ''; ?>">
                                <strong><?php echo htmlspecialchars($c['prenom'] . ' ' . $c['nom']); ?></strong>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-8 col-lg-9">
                <?php if (!$autre && empty($contacts)): ?>
                    <div class="card shadow-sm border-0 bg-light">
                        <div class="card-body p-4">
                            <h5 class="card-title"><i class="fas fa-info-circle text-primary me-2"></i>Pourquoi aucune conversation ?</h5>
                            <p class="mb-2">Les discussions apparaissent ici <strong>dès qu’une commande existe</strong> entre vous et une autre personne (client ou freelance).</p>
                            <?php if (Auth::isClient()): ?>
                                <p class="mb-2">En tant que <strong>client</strong> : allez sur <strong>Services</strong>, choisissez un service et cliquez sur <strong>Commander</strong>. Le freelance avec qui vous avez passé commande apparaîtra dans la liste à gauche et vous pourrez lui écrire.</p>
                                <a href="services.php" class="btn btn-primary btn-sm"><i class="fas fa-shopping-cart me-1"></i>Voir les services</a>
                            <?php else: ?>
                                <p class="mb-2">En tant que <strong>freelance</strong> : dès qu’un client vous envoie une commande (depuis vos services), il apparaîtra ici. En attendant, publiez des services dans <strong>Mes Services</strong>.</p>
                                <a href="mes-services.php" class="btn btn-primary btn-sm"><i class="fas fa-briefcase me-1"></i>Mes services</a>
                            <?php endif; ?>
                            <hr class="my-3">
                            <p class="small text-muted mb-0">Pour tester avec des données prêtes : exécutez <a href="init_demo.php">init_demo.php</a>, puis connectez-vous avec <kbd>aziz@freelanci.tn</kbd> ou <kbd>amine@freelanci.tn</kbd> (mot de passe <kbd>demo123</kbd>). Une commande exemple existe déjà entre eux.</p>
                        </div>
                    </div>
                <?php elseif (!$autre): ?>
                    <p class="text-muted">Sélectionnez une conversation dans la liste à gauche.</p>
                <?php else: ?>
                    <div class="card shadow">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span><?php echo htmlspecialchars($autre['prenom'] . ' ' . $autre['nom']); ?></span>
                            <a href="messages.php" class="btn btn-sm btn-outline-secondary">Toutes les conversations</a>
                        </div>
                        <div class="card-body" style="max-height: 450px; overflow-y: auto;">
                            <?php foreach ($messages as $m): ?>
                                <div class="mb-3 <?php echo $m['expediteur_id'] == $user_id ? 'text-end' : ''; ?>">
                                    <div class="d-inline-block p-2 rounded <?php echo $m['expediteur_id'] == $user_id ? 'bg-primary text-white' : 'bg-light'; ?>">
                                        <?php echo nl2br(htmlspecialchars($m['contenu'])); ?>
                                    </div>
                                    <div class="small text-muted"><?php echo date('d/m/Y H:i', strtotime($m['date_envoi'])); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="card-footer">
                            <form method="POST" class="d-flex gap-2">
                                <input type="text" name="contenu" class="form-control" placeholder="Votre message..." required>
                                <button type="submit" class="btn btn-primary">Envoyer</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
