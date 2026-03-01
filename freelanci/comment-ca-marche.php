<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';

$pageTitle = 'Comment ça marche';
$pdo = getDBConnection();
$stats_services = $pdo->query("SELECT COUNT(*) FROM services WHERE statut = 'actif'")->fetchColumn();
$stats_categories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();

include __DIR__ . '/includes/header.php';
?>

<header class="masthead text-center text-white" style="padding-top: 100px; padding-bottom: 60px;">
    <div class="masthead-content">
        <div class="container px-5">
            <h1 class="masthead-heading mb-0">Comment ça marche</h1>
            <h2 class="masthead-subheading mb-0">Une plateforme simple et sécurisée pour freelances et clients</h2>
            <p class="lead mt-3">Conformément à l'énoncé du projet : inscription, commande, réception des demandes</p>
        </div>
    </div>
</header>

<section class="py-5">
    <div class="container px-5">
        <div class="row mb-5">
            <div class="col-lg-12 text-center">
                <h2 class="display-6 mb-4">Chiffres clés</h2>
                <div class="d-flex justify-content-center gap-5 flex-wrap">
                    <div class="border rounded p-4 bg-light">
                        <span class="display-4 text-primary d-block"><?php echo (int)$stats_categories; ?></span>
                        <span class="text-muted">Catégories</span>
                    </div>
                    <div class="border rounded p-4 bg-light">
                        <span class="display-4 text-primary d-block"><?php echo (int)$stats_services; ?></span>
                        <span class="text-muted">Services actifs</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comment faire une discussion (Messages) -->
        <div class="card shadow-lg mb-5 border-info" id="messages">
            <div class="card-header bg-info text-white py-3">
                <h3 class="mb-0"><i class="fas fa-comments me-2"></i>Comment faire une discussion (Messages)</h3>
            </div>
            <div class="card-body p-4">
                <p class="lead">Les <strong>Messages</strong> permettent d’échanger avec un client ou un freelance avec qui vous avez une commande en commun.</p>
                <p><strong>Prérequis :</strong></p>
                <ul>
                    <li>Exécuter <strong>install_migration.php</strong> si ce n’est pas déjà fait (tables <code>messages</code>, <code>commandes</code>).</li>
                    <li>Exécuter <strong>init_demo.php</strong> (comptes démo + une commande exemple : Aziz → Amine).</li>
                </ul>
                <p><strong>Exemple pour tester une discussion :</strong></p>
                <ol>
                    <li><strong>Client</strong> : se connecter avec <kbd>aziz@freelanci.tn</kbd> / <kbd>demo123</kbd> → menu <strong>Messages</strong>.</li>
                    <li>Dans la liste à gauche, cliquer sur <strong>Amine Tlili</strong> (freelance avec qui une commande existe).</li>
                    <li>Saisir un message en bas de page et cliquer <strong>Envoyer</strong>.</li>
                    <li><strong>Freelance</strong> : se connecter avec <kbd>amine@freelanci.tn</kbd> / <kbd>demo123</kbd> → <strong>Messages</strong> → cliquer sur <strong>Aziz Chaabane</strong> → voir le message et répondre.</li>
                    <li>Revenir en tant que client : la réponse d’Amine s’affiche. La discussion est en place.</li>
                </ol>
                <p class="mb-0 text-muted small">Pour avoir d’autres conversations : un client doit <strong>commander un service</strong> (Services → Commander). Dès qu’une commande existe entre ce client et un freelance, les deux voient leur contact dans Messages.</p>
                <?php if (isLoggedIn()): ?>
                    <div class="mt-3">
                        <a href="messages.php" class="btn btn-info text-white">Ouvrir Messages</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Étapes globales (énoncé) -->
        <div class="card shadow">
            <div class="card-header bg-dark text-white py-3">
                <h3 class="mb-0"><i class="fas fa-list-check me-2"></i>Résumé conforme à l’énoncé</h3>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-2" style="width:60px;height:60px;">1</div>
                        <h6>Inscription / Connexion</h6>
                        <p class="small text-muted">Compte obligatoire pour accéder au site</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-2" style="width:60px;height:60px;">2</div>
                        <h6>Services</h6>
                        <p class="small text-muted">Liste des services, filtres, commande</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-2" style="width:60px;height:60px;">3</div>
                        <h6>Profil & commandes</h6>
                        <p class="small text-muted">Modifier profil, voir commandes, recevoir les demandes</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-2" style="width:60px;height:60px;">4</div>
                        <h6>Back Office</h6>
                        <p class="small text-muted">Admin : utilisateurs, services, catégories</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
