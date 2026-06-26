<?php

session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$role = $_SESSION['role_name'];

// US4-A — Planning d'un bénévole (trié chronologiquement)
$planning = [];
if ($role === 'volunteer') {
    $vol = $pdo->prepare("SELECT id FROM volunteer WHERE user_id = :uid");
    $vol->execute([':uid' => $_SESSION['user_id']]);
    $volunteer_id = $vol->fetchColumn();

    if ($volunteer_id) {
        $stmt = $pdo->prepare("
            SELECT
                m.id,
                m.title,
                m.location,
                m.starts_at,
                m.ends_at,
                m.status,
                s.name  AS skill_name,
                z.name  AS zone_name,
                a.status AS assignment_status
            FROM assignment a
            INNER JOIN mission m ON m.id = a.mission_id
            LEFT JOIN skill s ON s.id = m.required_skill_id
            LEFT JOIN zone z  ON z.id  = m.zone_id
            WHERE a.volunteer_id = :vid
              AND a.status = 'confirmed'
            ORDER BY m.starts_at ASC
        ");
        $stmt->execute([':vid' => $volunteer_id]);
        $planning = $stmt->fetchAll();
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">

    <div class="page-header">
        <h1>Mon planning</h1>
    </div>

    <?php if (empty($planning)) : ?>
        <p class="empty-state">Aucune mission affectée pour le moment.</p>
    <?php else : ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Mission</th>
                    <th>Lieu</th>
                    <th>Zone</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Compétence</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($planning as $p) : ?>
                    <tr>
                        <td><?= htmlspecialchars($p['title']) ?></td>
                        <td><?= htmlspecialchars($p['location']) ?></td>
                        <td><?= $p['zone_name'] ? htmlspecialchars($p['zone_name']) : '—' ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($p['starts_at'])) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($p['ends_at'])) ?></td>
                        <td><?= $p['skill_name'] ? htmlspecialchars($p['skill_name']) : '—' ?></td>
                        <td><span class="badge badge-<?= $p['status'] ?>"><?= $p['status'] ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>