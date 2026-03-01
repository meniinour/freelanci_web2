<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';
$gouvernorats = require __DIR__ . '/config/gouvernorats.php';

$pageTitle = 'Inscription';
$error = '';
$success = '';

Auth::requireGuest();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $telephone = trim($_POST['telephone'] ?? '');
    $type_utilisateur = $_POST['type_utilisateur'] ?? 'client';
    $gouvernorat = trim($_POST['gouvernorat'] ?? '');

    if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    } elseif ($password !== $password_confirm) {
        $error = 'Les mots de passe ne correspondent pas.';
    } elseif (strlen($password) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse email invalide.';
    } elseif (Utilisateur::emailExists($email)) {
        $error = 'Cet email est déjà utilisé.';
    } else {
        $data = [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'mot_de_passe' => password_hash($password, PASSWORD_DEFAULT),
            'telephone' => $telephone ?: null,
            'type_utilisateur' => $type_utilisateur,
            'gouvernorat' => $gouvernorat ?: null,
        ];
        if (Utilisateur::create($data)) {
            $success = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
        } else {
            $error = 'Erreur lors de l\'inscription. Veuillez réessayer.';
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<!-- Header -->
<header class="masthead text-center text-white" style="padding-top: 100px; padding-bottom: 50px;">
    <div class="masthead-content">
        <div class="container px-5">
            <h1 class="masthead-heading mb-0">Inscription</h1>
            <h2 class="masthead-subheading mb-0">Rejoignez la communauté Freelanci</h2>
        </div>
    </div>
</header>

<!-- Section d'inscription -->
<section style="padding: 80px 0;">
    <div class="container px-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg">
                    <div class="card-body p-5">
                        <h3 class="card-title text-center mb-4">Créer un compte</h3>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                                <br><a href="login.php" class="alert-link">Cliquez ici pour vous connecter</a>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nom" class="form-label">Nom *</label>
                                    <input type="text" class="form-control" id="nom" name="nom" 
                                           value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="prenom" class="form-label">Prénom *</label>
                                    <input type="text" class="form-control" id="prenom" name="prenom" 
                                           value="<?php echo htmlspecialchars($_POST['prenom'] ?? ''); ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="telephone" name="telephone" 
                                       value="<?php echo htmlspecialchars($_POST['telephone'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="gouvernorat" class="form-label">Gouvernorat (Tunisie)</label>
                                <select class="form-select" id="gouvernorat" name="gouvernorat">
                                    <option value="">— Choisir —</option>
                                    <?php foreach ($gouvernorats as $g): ?>
                                        <option value="<?php echo htmlspecialchars($g); ?>" <?php echo (($_POST['gouvernorat'] ?? '') === $g) ? 'selected' : ''; ?>><?php echo htmlspecialchars($g); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="type_utilisateur" class="form-label">Je suis *</label>
                                <select class="form-select" id="type_utilisateur" name="type_utilisateur" required>
                                    <option value="client" <?php echo (($_POST['type_utilisateur'] ?? '') === 'client') ? 'selected' : ''; ?>>
                                        Un client (je cherche des services)
                                    </option>
                                    <option value="freelance" <?php echo (($_POST['type_utilisateur'] ?? '') === 'freelance') ? 'selected' : ''; ?>>
                                        Un freelance (je propose des services)
                                    </option>
                                </select>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Mot de passe *</label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           minlength="6" required>
                                    <small class="form-text text-muted">Minimum 6 caractères</small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirm" class="form-label">Confirmer le mot de passe *</label>
                                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>S'inscrire
                                </button>
                            </div>
                        </form>
                        
                        <hr class="my-4">
                        
                        <p class="text-center mb-0">
                            Déjà inscrit ? 
                            <a href="login.php" class="text-primary">Connectez-vous ici</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
