<?php

session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

if (!in_array($_SESSION['role_name'], ['manager', 'admin'], true)) {
    header('Location: ../index.php');
    exit;
}

$missions = getCouvertureToutes($pdo);

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">

    <div class="page-header">
        <div>
            <h1>Toutes les missions</h1>
            <p class="page-subtitle">Vue globale des missions de tous les coordinateurs.</p>
        </div>
        <div style="display:flex;gap:10px">
            <a href="missions_sous_dotees.php" class="btn btn-secondary">Missions sous-dotées</a>
            <a href="dashboard.php" class="btn btn-secondary">← Retour</a>
        </div>
    </div>

    <?php if (empty($missions)) : ?>
        <p class="empty-state">Aucune mission enregistrée pour le moment.</p>
    <?php else : ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Mission</th>
                    <th>Lieu</th>
                    <th>Zone</th>
                    <th>Coordinateur</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Couverture</th>
                    <th>Progression</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($missions as $mission) : ?>
                    <tr class="<?= (int) $mission['est_sous_dotee'] === 1 ? 'row-warning' : '' ?>">
                        <td><strong><?= htmlspecialchars($mission['title']) ?></strong></td>
                        <td><?= htmlspecialchars($mission['location']) ?></td>
                        <td><?= htmlspecialchars($mission['zone_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($mission['coordinateur_name']) ?></td>
                        <td><?= htmlspecialchars(formatDateTimeFr($mission['starts_at'])) ?></td>
                        <td><?= htmlspecialchars(formatDateTimeFr($mission['ends_at'])) ?></td>
                        <td>
                            <strong><?= (int) $mission['nb_assigned'] ?> / <?= (int) $mission['required_capacity'] ?></strong>
                            <?php if ((int) $mission['est_sous_dotee'] === 1) : ?>
                                <span class="badge badge-warning">Sous-dotée</span>
                            <?php else : ?>
                                <span class="badge badge-confirmed">Complète</span>
                            <?php endif; ?>
                        </td>
                        <td style="min-width:120px">
                            <div style="background:#e0e0e0;border-radius:4px;height:8px;overflow:hidden">
                                <div style="
                                    width:<?= min((int) $mission['taux_pct'], 100) ?>%;
                                    background:<?= (int) $mission['est_sous_dotee'] === 1 ? '#ffc107' : '#28a745' ?>;
                                    height:100%
                                "></div>
                            </div>
                            <span style="font-size:12px;color:#888"><?= (int) $mission['taux_pct'] ?>%</span>
                        </td>
                        <td>
                            <span class="badge badge-<?= htmlspecialchars($mission['status']) ?>">
                                <?= htmlspecialchars($mission['status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="planning_missions.php?mission_id=<?= (int) $mission['id'] ?>" class="btn btn-sm">
                                Planning
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
