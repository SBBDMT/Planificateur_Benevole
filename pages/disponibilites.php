<?php

session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'volunteer') {
    header('Location: ../login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Récupération du bénévole
$stmt = $pdo->prepare("
    SELECT id
    FROM volunteer
    WHERE user_id = ?
");
$stmt->execute([$userId]);

$volunteer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$volunteer) {
    die("Profil bénévole introuvable.");
}

$volunteerId = $volunteer['id'];

// Liste des disponibilités
$stmt = $pdo->prepare("
    SELECT *
    FROM availability
    WHERE volunteer_id = ?
    ORDER BY starts_at
");
$stmt->execute([$volunteerId]);

$disponibilites = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">

    <div class="page-header">
        <h1>Mes disponibilités</h1>
    </div>

    <form action="../actions/save_disponibilite.php" method="POST" class="form-card">

        <div class="form-row">
            <div class="form-group">
                <label for="starts_at">Début <span class="required">*</span></label>
                <input id="starts_at" type="datetime-local" name="starts_at" required>
            </div>

            <div class="form-group">
                <label for="ends_at">Fin <span class="required">*</span></label>
                <input id="ends_at" type="datetime-local" name="ends_at" required>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>

    </form>

    <section class="dashboard-section">
        <div class="section-header">
            <h2>Disponibilités enregistrées</h2>
        </div>

        <?php if (empty($disponibilites)) : ?>

            <p class="empty-state">Aucune disponibilité enregistrée.</p>

        <?php else : ?>

            <table class="table">
                <thead>
                    <tr>
                        <th>Début</th>
                        <th>Fin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($disponibilites as $disponibilite) : ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($disponibilite['starts_at'])) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($disponibilite['ends_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php endif; ?>
    </section>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
