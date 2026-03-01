<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';

$pageTitle = 'Connexion';
$error = '';
$success = '';

Auth::requireGuest();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $user = Utilisateur::findByEmail($email);
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            if ($user['statut'] === 'bloque') {
                $error = 'Votre compte a été bloqué. Contactez l\'administrateur.';
            } else {
                Auth::login($user);
                $redirect = $_GET['redirect'] ?? '';
                if ($user['type_utilisateur'] === 'admin') {
                    header('Location: admin/index.php');
                } elseif (!empty($redirect) && preg_match('/^[a-zA-Z0-9_\-\.]+\.php(\?.*)?$/', $redirect)) {
                    header('Location: ' . $redirect);
                } else {
                    header('Location: index.php');
                }
                exit;
            }
        } else {
            $error = 'Email ou mot de passe incorrect.';
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<header class="masthead text-center text-white" style="padding-top: 100px; padding-bottom: 50px;">
    <div class="masthead-content">
        <div class="container px-5">
            <h1 class="masthead-heading mb-0">Connexion</h1>
            <h2 class="masthead-subheading mb-0">Accédez à votre espace Freelanci</h2>
        </div>
    </div>
</header>

<section style="padding: 80px 0;">
    <div class="container px-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-lg">
                    <div class="card-body p-5">
                        <h3 class="card-title text-center mb-4">Se connecter</h3>
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                                </button>
                            </div>
                        </form>
                        <hr class="my-4">
                        <p class="text-center mb-0">
                            Pas encore de compte ?
                            <a href="register.php" class="text-primary">Inscrivez-vous ici</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
