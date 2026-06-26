<?php

session_start();
require_once __DIR__ . '/../config/db.php';

// Accessible au coordinateur (ses logs) et au manager (tous les logs)
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_name'], ['coordinator', 'manager'])) {
    header('Location: ../login.php');
    exit;
}

$is_manager   = $_SESSION['role_name'] === 'manager';
$mission_id   = (int) ($_GET['mission_id'] ?? 0);

// Filtre sur les actions liées aux affectations uniquement
$actions_affectation = ['assignment.created', 'assignment.cancelled'];
$placeholders = implode(',', array_fill(0, count($actions_affectation), '?'));

if ($is_manager) {
    // Manager : tous les logs d'affectation
    $sql = "
        SELECT
            al.id,
            al.action,
            al.entity_id,
            al.created_at,
            u.name  AS auteur,
            m.title AS mission_title,
            uv.name AS benevole_name
        FROM audit_log al
        LEFT JOIN user u ON u.id = al.user_id
        LEFT JOIN assignment a ON a.id = al.entity_id
        LEFT JOIN mission m ON m.id = a.mission_id
        LEFT JOIN volunteer v ON v.id = a.volunteer_id
        LEFT JOIN user uv ON uv.id = v.user_id
        WHERE al.action IN ($placeholders)
        ORDER BY al.created_at DESC
        LIMIT 100
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($actions_affectation);
} else {
    // Coordinateur : seulement ses missions
    $sql = "
        SELECT
            al.id,
            al.action,
            al.entity_id,
            al.created_at,
            u.name  AS auteur,
            m.title AS mission_title,
            uv.name AS benevole_name
        FROM audit_log al
        LEFT JOIN user u ON u.id = al.user_id
        LEFT JOIN assignment a ON a.id = al.entity_id
        LEFT JOIN mission m ON m.id = a.mission_id
        LEFT JOIN volunteer v ON v.id = a.volunteer_id
        LEFT JOIN user uv ON uv.id = v.user_id
        WHERE al.action IN ($placeholders)
          AND m.coordinator_id = ?
        ORDER BY al.created_at DESC
        LIMIT 100
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([...$actions_affectation, $_SESSION['user_id']]);
}

$logs = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">

    <div class="page-header">
        <h1>Journal des affectations</h1>
        <a href="<?= $is_manager ? 'dashboard.php' : 'mes_missions.php' ?>" class="btn btn-secondary">← Retour</a>
    </div>

    <?php if (empty($logs)) : ?>
        <p class="empty-state">Aucune action enregistrée pour le moment.</p>
    <?php else : ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Action</th>
                    <th>Mission</th>
                    <th>Bénévole</th>
                    <th>Par</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log) :
                    $is_cancel = $log['action'] === 'assignment.cancelled';
                ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></td>
                        <td>
                            <?php if ($is_cancel) : ?>
                                <span class="badge badge-cancelled">Désaffectation</span>
                            <?php else : ?>
                                <span class="badge badge-confirmed">Affectation</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $log['mission_title'] ? htmlspecialchars($log['mission_title']) : '—' ?></td>
                        <td><?= $log['benevole_name'] ? htmlspecialchars($log['benevole_name']) : '—' ?></td>
                        <td><?= $log['auteur'] ? htmlspecialchars($log['auteur']) : 'Système' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>