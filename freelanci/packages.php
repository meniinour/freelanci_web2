<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';

$pageTitle = 'Packages';
requireLogin();
if (!isFreelance()) {
    header('Location: index.php');
    exit;
}

$pdo = getDBConnection();
$user_id = $_SESSION['user_id'];
$service_id = isset($_GET['service_id']) ? intval($_GET['service_id']) : 0;

$service = null;
if ($service_id) {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ? AND freelance_id = ?");
    $stmt->execute([$service_id, $user_id]);
    $service = $stmt->fetch();
}

$packages = [];
if ($service) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM packages WHERE service_id = ? ORDER BY FIELD(nom, 'basic', 'standard', 'premium')");
        $stmt->execute([$service_id]);
        $packages = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // nom => row, need index by nom
        $stmt = $pdo->prepare("SELECT * FROM packages WHERE service_id = ? ORDER BY FIELD(nom, 'basic', 'standard', 'premium')");
        $stmt->execute([$service_id]);
        $packages = [];
        while ($row = $stmt->fetch()) $packages[$row['nom']] = $row;
    } catch (Exception $e) {}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $service && isset($_POST['sauvegarder'])) {
    $noms = ['basic' => 'Basic', 'standard' => 'Standard', 'premium' => 'Premium'];
    foreach ($noms as $key => $label) {
        $prix = floatval($_POST["prix_$key"] ?? 0);
        $delai = (int)($_POST["delai_$key"] ?? 7);
        $desc = trim($_POST["desc_$key"] ?? '');
        if ($prix <= 0) continue;
        try {
            $pdo->prepare("INSERT INTO packages (service_id, nom, prix, delai_jours, description) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE prix = VALUES(prix), delai_jours = VALUES(delai_jours), description = VALUES(description)")
                ->execute([$service_id, $key, $prix, $delai, $desc]);
        } catch (Exception $e) {}
    }
    header('Location: packages.php?service_id=' . $service_id . '&ok=1');
    exit;
}

include __DIR__ . '/includes/header.php';
?>

<header class="masthead text-center text-white" style="padding-top: 100px; padding-bottom: 50px;">
    <div class="masthead-content">
        <div class="container px-5">
            <h1 class="masthead-heading mb-0">Packages</h1>
            <h2 class="masthead-subheading mb-0"><?php echo $service ? htmlspecialchars($service['titre']) : 'Service'; ?></h2>
        </div>
    </div>
</header>

<section style="padding: 80px 0;">
    <div class="container px-5">
        <?php if (isset($_GET['ok'])): ?>
            <div class="alert alert-success">Packages enregistrés.</div>
        <?php endif; ?>
        <?php if (!$service): ?>
            <div class="alert alert-warning">Service introuvable.</div>
            <a href="mes-services.php" class="btn btn-primary">Retour à mes services</a>
        <?php else: ?>
            <div class="card shadow">
                <div class="card-body">
                    <p class="text-muted">Définissez jusqu'à 3 offres (Basic, Standard, Premium) pour ce service. Laissez le prix à 0 pour ne pas proposer une offre.</p>
                    <form method="POST">
                        <input type="hidden" name="sauvegarder" value="1">
                        <?php foreach (['basic' => 'Basic', 'standard' => 'Standard', 'premium' => 'Premium'] as $key => $label): 
                            $p = $packages[$key] ?? [];
                            ?>
                            <div class="border rounded p-3 mb-3">
                                <h5><?php echo $label; ?></h5>
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <label class="form-label">Prix (TND)</label>
                                        <input type="number" step="0.01" min="0" class="form-control" name="prix_<?php echo $key; ?>" value="<?php echo $p['prix'] ?? ''; ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Délai (jours)</label>
                                        <input type="number" min="1" class="form-control" name="delai_<?php echo $key; ?>" value="<?php echo $p['delai_jours'] ?? 7; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Description</label>
                                        <input type="text" class="form-control" name="desc_<?php echo $key; ?>" value="<?php echo htmlspecialchars($p['description'] ?? ''); ?>" placeholder="Inclus...">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <button type="submit" class="btn btn-primary">Enregistrer les packages</button>
                        <a href="mes-services.php" class="btn btn-secondary">Retour</a>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
