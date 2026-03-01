<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';
$gouvernorats = require __DIR__ . '/config/gouvernorats.php';

$pageTitle = 'Mon Profil';

Auth::requireLogin();

$user_id = Auth::getUserId();
$error = '';
$success = '';

$user = Utilisateur::find($user_id);
if (!$user) {
    header('Location: logout.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_profil'])) {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $gouvernorat = trim($_POST['gouvernorat'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $competences = trim($_POST['competences'] ?? '');
    if (empty($nom) || empty($prenom) || empty($email)) {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse email invalide.';
    } elseif (Utilisateur::emailExists($email, $user_id)) {
        $error = 'Cet email est déjà utilisé par un autre compte.';
    } elseif (Utilisateur::update($user_id, ['nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'telephone' => $telephone ?: null, 'gouvernorat' => $gouvernorat ?: null, 'bio' => $bio ?: null, 'competences' => $competences ?: null])) {
        $_SESSION['user_nom'] = $nom;
        $_SESSION['user_prenom'] = $prenom;
        $_SESSION['user_email'] = $email;
        $success = 'Profil mis à jour avec succès !';
        $user = Utilisateur::find($user_id);
    } else {
        $error = 'Erreur lors de la mise à jour.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['changer_mdp'])) {
    $ancien_mdp = $_POST['ancien_mdp'] ?? '';
    $nouveau_mdp = $_POST['nouveau_mdp'] ?? '';
    $confirmer_mdp = $_POST['confirmer_mdp'] ?? '';
    if (empty($ancien_mdp) || empty($nouveau_mdp) || empty($confirmer_mdp)) {
        $error = 'Veuillez remplir tous les champs.';
    } elseif (!password_verify($ancien_mdp, $user['mot_de_passe'])) {
        $error = 'Ancien mot de passe incorrect.';
    } elseif ($nouveau_mdp !== $confirmer_mdp) {
        $error = 'Les nouveaux mots de passe ne correspondent pas.';
    } elseif (strlen($nouveau_mdp) < 6) {
        $error = 'Le nouveau mot de passe doit contenir au moins 6 caractères.';
    } elseif (Utilisateur::update($user_id, ['mot_de_passe' => $nouveau_mdp])) {
        $success = 'Mot de passe modifié avec succès !';
    } else {
        $error = 'Erreur lors de la modification du mot de passe.';
    }
}

$portfolio = isFreelance() ? Portfolio::getByFreelance($user_id) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajout_portfolio']) && isFreelance()) {
    $titre = trim($_POST['port_titre'] ?? '');
    $type_media = in_array($_POST['port_type'] ?? '', ['image','video','pdf','lien']) ? $_POST['port_type'] : 'lien';
    $url = trim($_POST['port_url'] ?? '');
    if ($titre !== '' && $url !== '' && Portfolio::add($user_id, $titre, $type_media, $url)) {
        $success = 'Portfolio ajouté.';
        $portfolio = Portfolio::getByFreelance($user_id);
    }
}

if (isset($_GET['supprimer_portfolio']) && isFreelance()) {
    $pid = (int)$_GET['supprimer_portfolio'];
    if (Portfolio::delete($pid, $user_id)) {
        header('Location: profile.php?supprime_portfolio=1');
        exit;
    }
    header('Location: profile.php?erreur_portfolio=1');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isFreelance() && isset($_POST['commande_action'])) {
    $cid = (int)($_POST['commande_id'] ?? 0);
    $action = $_POST['commande_action'] ?? '';
    if ($action === 'accepter') {
        if (Commande::updateStatut($cid, 'en_cours', $user_id)) $success = 'Commande acceptée.';
    } elseif ($action === 'refuser') {
        if (Commande::updateStatut($cid, 'refusee', $user_id)) $success = 'Commande refusée.';
    } elseif ($action === 'livrer') {
        $url = trim($_POST['livrable_url'] ?? '');
        if ($url !== '' && Commande::setLivrable($cid, $url, $user_id)) $success = 'Livrable enregistré.';
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isClient() && isset($_POST['terminer_commande'])) {
    $cid = (int)($_POST['commande_id'] ?? 0);
    if (Commande::terminerParClient($cid, $user_id)) $success = 'Commande marquée comme terminée.';
}

$commandes = isClient() ? Commande::getByClient($user_id) : [];
$commandes_avec_avis = [];
if (isClient()) {
    try {
        $pdo = getDBConnection();
        $pdo->query("SELECT 1 FROM avis LIMIT 1");
        foreach ($commandes as $c) {
            if ($c['statut'] === 'terminee') {
                $st = $pdo->prepare("SELECT id FROM avis WHERE commande_id = ?");
                $st->execute([$c['id']]);
                $commandes_avec_avis[$c['id']] = $st->fetch();
            }
        }
    } catch (Exception $e) {}
}
$commandes_recues = isFreelance() ? Commande::getByFreelance($user_id) : [];

include __DIR__ . '/includes/header.php';
?>

<!-- Header -->
<header class="masthead text-center text-white" style="padding-top: 100px; padding-bottom: 50px;">
    <div class="masthead-content">
        <div class="container px-5">
            <h1 class="masthead-heading mb-0">Mon Profil</h1>
            <h2 class="masthead-subheading mb-0">Gérez vos informations personnelles</h2>
        </div>
    </div>
</header>

<!-- Section Profil -->
<section style="padding: 80px 0;">
    <div class="container px-5">
        
        <?php if (isset($_GET['supprime_portfolio'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>Publication portfolio supprimée.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
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
        
        <div class="row">
            <!-- Informations personnelles -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Informations personnelles</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom *</label>
                                <input type="text" class="form-control" id="nom" name="nom" 
                                       value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="prenom" class="form-label">Prénom *</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" 
                                       value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="telephone" name="telephone" 
                                       value="<?php echo htmlspecialchars($user['telephone'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="gouvernorat" class="form-label">Gouvernorat</label>
                                <select class="form-select" id="gouvernorat" name="gouvernorat">
                                    <option value="">— Choisir —</option>
                                    <?php foreach ($gouvernorats as $g): ?>
                                        <option value="<?php echo htmlspecialchars($g); ?>" <?php echo (($user['gouvernorat'] ?? '') === $g) ? 'selected' : ''; ?>><?php echo htmlspecialchars($g); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php if (isFreelance()): ?>
                            <div class="mb-3">
                                <label for="bio" class="form-label">Bio / Présentation</label>
                                <textarea class="form-control" id="bio" name="bio" rows="3" placeholder="Présentez-vous en quelques lignes..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="competences" class="form-label"><i class="fas fa-tools me-1"></i>Déposer mes compétences</label>
                                <input type="text" class="form-control" id="competences" name="competences" 
                                       value="<?php echo htmlspecialchars($user['competences'] ?? ''); ?>" 
                                       placeholder="Ex: Montage vidéo, Figma, React, SEO, Rédaction, Design...">
                                <small class="form-text text-muted">Indiquez les compétences que vous maîtrisez (séparées par des virgules).</small>
                            </div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <label class="form-label">Type de compte</label>
                                <input type="text" class="form-control" 
                                       value="<?php echo ucfirst($user['type_utilisateur']); ?>" disabled>
                            </div>
                            
                            <button type="submit" name="modifier_profil" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer les modifications
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Changement de mot de passe -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-lg">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Changer le mot de passe</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="ancien_mdp" class="form-label">Ancien mot de passe *</label>
                                <input type="password" class="form-control" id="ancien_mdp" name="ancien_mdp" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="nouveau_mdp" class="form-label">Nouveau mot de passe *</label>
                                <input type="password" class="form-control" id="nouveau_mdp" name="nouveau_mdp" 
                                       minlength="6" required>
                                <small class="form-text text-muted">Minimum 6 caractères</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirmer_mdp" class="form-label">Confirmer le nouveau mot de passe *</label>
                                <input type="password" class="form-control" id="confirmer_mdp" name="confirmer_mdp" required>
                            </div>
                            
                            <button type="submit" name="changer_mdp" class="btn btn-warning">
                                <i class="fas fa-key me-2"></i>Changer le mot de passe
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Portfolio (freelances) -->
        <?php if (isFreelance()): ?>
            <div class="card shadow-lg mt-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-images me-2"></i>Mon portfolio</h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="row g-3 mb-4">
                        <input type="hidden" name="ajout_portfolio" value="1">
                        <div class="col-md-4">
                            <label class="form-label">Titre</label>
                            <input type="text" class="form-control" name="port_titre" required placeholder="Ex: Maquette site X">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Type</label>
                            <select class="form-select" name="port_type">
                                <option value="image">Image</option>
                                <option value="video">Vidéo</option>
                                <option value="pdf">PDF</option>
                                <option value="lien">Lien</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">URL</label>
                            <input type="url" class="form-control" name="port_url" required placeholder="https://...">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Ajouter</button>
                        </div>
                    </form>
                    <?php if (!empty($portfolio)): ?>
                        <div class="row g-2">
                            <?php foreach ($portfolio as $p): ?>
                                <div class="col-md-4">
                                    <div class="border rounded p-2 d-flex justify-content-between align-items-center">
                                        <span><?php echo htmlspecialchars($p['titre']); ?> (<?php echo $p['type_media']; ?>)</span>
                                        <a href="?supprimer_portfolio=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette publication du portfolio ?');" title="Supprimer publication"><i class="fas fa-trash me-1"></i>Supprimer publication</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">Aucun élément. Ajoutez des liens ou médias pour votre portfolio.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Historique des commandes (pour les clients) -->
        <?php if (isClient() && !empty($commandes)): ?>
            <div class="card shadow-lg mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Mes commandes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Freelance</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($commandes as $commande): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($commande['service_titre']); ?></td>
                                        <td><?php echo htmlspecialchars($commande['freelance_prenom'] . ' ' . $commande['freelance_nom']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($commande['date_commande'])); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $commande['statut'] === 'acceptee' || $commande['statut'] === 'en_cours' ? 'success' : 
                                                    ($commande['statut'] === 'refusee' ? 'danger' : 
                                                    ($commande['statut'] === 'terminee' || $commande['statut'] === 'livree' ? 'info' : 'warning')); 
                                            ?>">
                                                <?php 
                                                $statuts = [
                                                    'en_attente' => 'En attente',
                                                    'acceptee' => 'Acceptée',
                                                    'refusee' => 'Refusée',
                                                    'en_cours' => 'En cours',
                                                    'livree' => 'Livrée',
                                                    'terminee' => 'Terminée'
                                                ];
                                                echo $statuts[$commande['statut']] ?? $commande['statut'];
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($commande['statut'] === 'livree'): ?>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="commande_id" value="<?php echo $commande['id']; ?>">
                                                    <input type="hidden" name="terminer_commande" value="1">
                                                    <button type="submit" class="btn btn-sm btn-success">Confirmer réception</button>
                                                </form>
                                            <?php endif; ?>
                                            <?php if ($commande['statut'] === 'terminee' && empty($commandes_avec_avis[$commande['id']])): ?>
                                                <a href="avis.php?id=<?php echo $commande['id']; ?>" class="btn btn-sm btn-outline-primary">Laisser un avis</a>
                                            <?php endif; ?>
                                            <a href="messages.php?commande=<?php echo $commande['id']; ?>" class="btn btn-sm btn-outline-secondary">Messages</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Commandes reçues (freelances) - conforme énoncé : recevoir les demandes -->
        <?php if (isFreelance()): ?>
            <div class="card shadow-lg mt-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0"><i class="fas fa-inbox me-2"></i>Offres / Commandes reçues</h5>
                    <div>
                        <a href="mes-offres.php" class="btn btn-sm btn-light text-dark me-1">Consulter toutes mes offres</a>
                        <a href="comment-ca-marche.php#recevoir" class="btn btn-sm btn-outline-light">Comment recevoir les demandes</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($commandes_recues)): ?>
                        <p class="text-muted mb-0">Vous n'avez pas encore de demande. Dès qu'un client commande un de vos services, elle apparaîtra ici. Proposez des services dans <a href="mes-services.php">Mes Services</a> pour recevoir des commandes.</p>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Client</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($commandes_recues as $cr): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($cr['service_titre']); ?></td>
                                        <td><?php echo htmlspecialchars($cr['client_prenom'] . ' ' . $cr['client_nom']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($cr['date_commande'])); ?></td>
                                        <td><span class="badge bg-secondary"><?php echo $cr['statut']; ?></span></td>
                                        <td>
                                            <?php if ($cr['statut'] === 'en_attente'): ?>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="commande_id" value="<?php echo $cr['id']; ?>">
                                                    <input type="hidden" name="commande_action" value="accepter">
                                                    <button type="submit" class="btn btn-sm btn-success">Accepter</button>
                                                </form>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="commande_id" value="<?php echo $cr['id']; ?>">
                                                    <input type="hidden" name="commande_action" value="refuser">
                                                    <button type="submit" class="btn btn-sm btn-danger">Refuser</button>
                                                </form>
                                            <?php endif; ?>
                                            <?php if (in_array($cr['statut'], ['en_cours', 'acceptee'])): ?>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="commande_id" value="<?php echo $cr['id']; ?>">
                                                    <input type="hidden" name="commande_action" value="livrer">
                                                    <input type="url" name="livrable_url" placeholder="URL du livrable" class="form-control form-control-sm d-inline-block w-auto" required>
                                                    <button type="submit" class="btn btn-sm btn-primary">Marquer livrée</button>
                                                </form>
                                            <?php endif; ?>
                                            <a href="messages.php?commande=<?php echo $cr['id']; ?>" class="btn btn-sm btn-outline-secondary">Messages</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
