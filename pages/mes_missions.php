<?php

session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'coordinator') {
    header('Location: ../login.php');
    exit;
}

// Message de succès après création
$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);

// Récupération uniquement des missions du coordinateur connecté
$stmt = $pdo->prepare("
    SELECT
        m.id,
        m.title,
        m.location,
        m.starts_at,
        m.ends_at,
        m.required_capacity,
        m.status,
        s.name AS skill_name,
        z.name AS zone_name,
        COUNT(a.id) AS nb_assigned
    FROM mission m
    LEFT JOIN skill s ON s.id = m.required_skill_id
    LEFT JOIN zone z  ON z.id = m.zone_id
    LEFT JOIN assignment a ON a.mission_id = m.id AND a.status = 'confirmed'
    WHERE m.coordinator_id = :uid
    GROUP BY m.id
    ORDER BY m.starts_at ASC
");
$stmt->execute([':uid' => $_SESSION['user_id']]);
$missions = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">

    <div class="page-header">
        <h1>Mes missions</h1>
        <div style="display:flex;gap:10px">
            <a href="missions_sous_dotees.php" class="btn btn-secondary">Missions sous-dotées</a>
            <a href="creer_mission.php" class="btn btn-primary">+ Créer une mission</a>
        </div>
    </div>

    <?php if ($success) : ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($missions)) : ?>
        <p class="empty-state">Vous n'avez pas encore créé de mission. <a href="creer_mission.php">Créer la première</a></p>
    <?php else : ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Lieu</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Capacité</th>
                    <th>Compétence</th>
                    <th>Zone</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($missions as $m) : ?>
                    <?php
                    $taux      = $m['required_capacity'] > 0 ? $m['nb_assigned'] / $m['required_capacity'] : 0;
                    $sous_dote = $taux < 1;
                    ?>
                    <tr class="<?= $sous_dote ? 'row-warning' : '' ?>">
                        <td><?= htmlspecialchars($m['title']) ?></td>
                        <td><?= htmlspecialchars($m['location']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($m['starts_at'])) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($m['ends_at'])) ?></td>
                        <td>
                            <?= $m['nb_assigned'] ?> / <?= $m['required_capacity'] ?>
                            <?php if ($sous_dote) : ?>
                                <span class="badge badge-warning">Sous-dotée</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $m['skill_name'] ? htmlspecialchars($m['skill_name']) : '—' ?></td>
                        <td><?= $m['zone_name'] ? htmlspecialchars($m['zone_name']) : '—' ?></td>
                        <td><span class="badge badge-<?= $m['status'] ?>"><?= $m['status'] ?></span></td>
                        <td>
                            <a href="mes_affectations.php?mission_id=<?= $m['id'] ?>" class="btn btn-sm">Affectations</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>