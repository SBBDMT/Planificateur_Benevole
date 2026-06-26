<?php

session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

if (!in_array($_SESSION['role_name'], ['manager', 'admin'], true)) {
    header('Location: ../index.php');
    exit;
}

$stmt = $pdo->query("
    SELECT
        a.id,
        a.status,
        a.created_at,
        m.id AS mission_id,
        m.title AS mission_title,
        m.location AS mission_location,
        m.starts_at,
        m.ends_at,
        coord.name AS coordinateur_name,
        benevole.name AS benevole_name,
        benevole.email AS benevole_email
    FROM assignment a
    INNER JOIN mission m ON m.id = a.mission_id
    INNER JOIN user coord ON coord.id = m.coordinator_id
    INNER JOIN volunteer v ON v.id = a.volunteer_id
    INNER JOIN user benevole ON benevole.id = v.user_id
    ORDER BY m.starts_at ASC, m.title ASC, a.created_at DESC
");
$affectations = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">

    <div class="page-header">
        <div>
            <h1>Toutes les affectations</h1>
            <p class="page-subtitle">Vue globale des bénévoles affectés aux missions.</p>
        </div>
        <div style="display:flex;gap:10px">
            <a href="audit_log.php" class="btn btn-secondary">Journal</a>
            <a href="dashboard.php" class="btn btn-secondary">← Retour</a>
        </div>
    </div>

    <?php if (empty($affectations)) : ?>
        <p class="empty-state">Aucune affectation enregistrée pour le moment.</p>
    <?php else : ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Mission</th>
                    <th>Créneau</th>
                    <th>Bénévole</th>
                    <th>Email</th>
                    <th>Coordinateur</th>
                    <th>Statut</th>
                    <th>Affecté le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($affectations as $affectation) : ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($affectation['mission_title']) ?></strong><br>
                            <span class="muted"><?= htmlspecialchars($affectation['mission_location']) ?></span>
                        </td>
                        <td>
                            <?= htmlspecialchars(date('d/m/Y H:i', strtotime($affectation['starts_at']))) ?><br>
                            <span class="muted">à <?= htmlspecialchars(date('d/m/Y H:i', strtotime($affectation['ends_at']))) ?></span>
                        </td>
                        <td><?= htmlspecialchars($affectation['benevole_name']) ?></td>
                        <td><?= htmlspecialchars($affectation['benevole_email']) ?></td>
                        <td><?= htmlspecialchars($affectation['coordinateur_name']) ?></td>
                        <td>
                            <span class="badge badge-<?= htmlspecialchars($affectation['status']) ?>">
                                <?= htmlspecialchars($affectation['status']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($affectation['created_at']))) ?></td>
                        <td>
                            <a href="planning_missions.php?mission_id=<?= (int) $affectation['mission_id'] ?>" class="btn btn-sm">
                                Mission
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
