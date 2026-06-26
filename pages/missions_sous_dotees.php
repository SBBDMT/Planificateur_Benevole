<?php

session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_name'], ['coordinator', 'manager', 'admin'], true)) {
    header('Location: ../login.php');
    exit;
}

$role = $_SESSION['role_name'];
$isManagerView = in_array($role, ['manager', 'admin'], true);

$filtreZone = (int) ($_GET['zone_id'] ?? 0);
$filtreStatut = trim($_GET['statut'] ?? '');
$statutsAutorises = ['draft', 'open', 'full', 'closed'];

if (!in_array($filtreStatut, $statutsAutorises, true)) {
    $filtreStatut = '';
}

$filters = [
    'sous_dotees_only' => true,
];

if ($filtreZone > 0) {
    $filters['zone_id'] = $filtreZone;
}

if ($filtreStatut !== '') {
    $filters['statut'] = $filtreStatut;
}

$coordinatorId = $isManagerView ? null : (int) $_SESSION['user_id'];
$missions = getCouvertureToutes($pdo, $coordinatorId, $filters);
$nbSousDotees = count($missions);

$zones = $pdo
    ->query('SELECT id, name FROM zone ORDER BY name ASC')
    ->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">

    <div class="page-header">
        <div>
            <h1>Missions sous-dotées</h1>
            <p class="page-subtitle">Missions dont la couverture est inférieure à la capacité demandée.</p>
        </div>
        <a href="<?= $isManagerView ? 'dashboard.php' : 'mes_missions.php' ?>" class="btn btn-secondary">← Retour</a>
    </div>

    <form method="GET" action="missions_sous_dotees.php" class="form-card" style="margin-bottom:24px">
        <div class="form-row" style="align-items:flex-end">

            <div class="form-group" style="margin-bottom:0">
                <label for="zone_id">Zone</label>
                <select id="zone_id" name="zone_id">
                    <option value="0">— Toutes les zones —</option>
                    <?php foreach ($zones as $zone) : ?>
                        <option value="<?= (int) $zone['id'] ?>" <?= $filtreZone === (int) $zone['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($zone['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom:0">
                <label for="statut">Statut</label>
                <select id="statut" name="statut">
                    <option value="">— Tous les statuts —</option>
                    <option value="draft" <?= $filtreStatut === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="open" <?= $filtreStatut === 'open' ? 'selected' : '' ?>>Open</option>
                    <option value="full" <?= $filtreStatut === 'full' ? 'selected' : '' ?>>Full</option>
                    <option value="closed" <?= $filtreStatut === 'closed' ? 'selected' : '' ?>>Closed</option>
                </select>
            </div>

            <div class="form-actions" style="margin-top:0">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="missions_sous_dotees.php" class="btn btn-secondary">Réinitialiser</a>
            </div>

        </div>
    </form>

    <?php if ($nbSousDotees === 0) : ?>
        <div class="alert alert-success">
            Toutes les missions sont couvertes<?= $filtreZone || $filtreStatut ? ' pour ce filtre' : '' ?>.
        </div>
    <?php else : ?>
        <div class="alert alert-error">
            <?= $nbSousDotees ?> mission<?= $nbSousDotees > 1 ? 's' : '' ?> sous-dotée<?= $nbSousDotees > 1 ? 's' : '' ?>
            <?= $filtreZone || $filtreStatut ? 'correspondent à ce filtre' : 'au total' ?>.
        </div>
    <?php endif; ?>

    <?php if ($nbSousDotees > 0) : ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Mission</th>
                    <th>Lieu</th>
                    <th>Zone</th>
                    <?php if ($isManagerView) : ?>
                        <th>Coordinateur</th>
                    <?php endif; ?>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Couverture</th>
                    <th>Manquants</th>
                    <th>Progression</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($missions as $mission) : ?>
                    <tr class="row-warning">
                        <td><strong><?= htmlspecialchars($mission['title']) ?></strong></td>
                        <td><?= htmlspecialchars($mission['location']) ?></td>
                        <td><?= htmlspecialchars($mission['zone_name'] ?? '—') ?></td>
                        <?php if ($isManagerView) : ?>
                            <td><?= htmlspecialchars($mission['coordinateur_name']) ?></td>
                        <?php endif; ?>
                        <td><?= htmlspecialchars(formatDateTimeFr($mission['starts_at'])) ?></td>
                        <td><?= htmlspecialchars(formatDateTimeFr($mission['ends_at'])) ?></td>
                        <td>
                            <strong><?= (int) $mission['nb_assigned'] ?> / <?= (int) $mission['required_capacity'] ?></strong>
                        </td>
                        <td>
                            <span class="badge badge-warning">
                                <?= (int) $mission['manquants'] ?> bénévole<?= (int) $mission['manquants'] > 1 ? 's' : '' ?>
                            </span>
                        </td>
                        <td style="min-width:120px">
                            <div style="background:#e0e0e0;border-radius:4px;height:8px;overflow:hidden">
                                <div style="width:<?= min((int) $mission['taux_pct'], 100) ?>%;background:#ffc107;height:100%"></div>
                            </div>
                            <span style="font-size:12px;color:#888"><?= (int) $mission['taux_pct'] ?>%</span>
                        </td>
                        <td>
                            <span class="badge badge-<?= htmlspecialchars($mission['status']) ?>">
                                <?= htmlspecialchars($mission['status']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>