<?php

session_start();
require_once __DIR__ . '/../config/db.php';

// Accessible coordinateur et manager
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_name'], ['coordinator', 'manager'])) {
    header('Location: ../login.php');
    exit;
}

$mission_id = (int) ($_GET['mission_id'] ?? 0);
$role       = $_SESSION['role_name'];

// US4-B — Récupération de la mission
$mission = null;
if ($mission_id > 0) {
    // Coordinateur : uniquement ses missions
    // Manager : toutes les missions
    $where_coord = $role === 'coordinator' ? "AND m.coordinator_id = :uid" : "";

    $sql = "
        SELECT
            m.id, m.title, m.location, m.starts_at, m.ends_at,
            m.required_capacity, m.status,
            s.name AS skill_name,
            z.name AS zone_name,
            u.name AS coordinateur_name,
            COUNT(a.id) AS nb_assigned
        FROM mission m
        LEFT JOIN skill s ON s.id = m.required_skill_id
        LEFT JOIN zone z  ON z.id  = m.zone_id
        LEFT JOIN user u  ON u.id  = m.coordinator_id
        LEFT JOIN assignment a ON a.mission_id = m.id AND a.status = 'confirmed'
        WHERE m.id = :mid $where_coord
        GROUP BY m.id
    ";

    $stmt = $pdo->prepare($sql);
    $params = [':mid' => $mission_id];
    if ($role === 'coordinator') $params[':uid'] = $_SESSION['user_id'];
    $stmt->execute($params);
    $mission = $stmt->fetch();
}

// US4-B — Liste des bénévoles affectés à cette mission
$benevoles_affectes = [];
if ($mission) {
    $stmt = $pdo->prepare("
        SELECT
            u.name          AS benevole_name,
            u.email         AS benevole_email,
            a.status        AS assignment_status,
            a.created_at    AS assigned_at
        FROM assignment a
        INNER JOIN volunteer v ON v.id = a.volunteer_id
        INNER JOIN user u      ON u.id = v.user_id
        WHERE a.mission_id = :mid
          AND a.status = 'confirmed'
        ORDER BY u.name ASC
    ");
    $stmt->execute([':mid' => $mission_id]);
    $benevoles_affectes = $stmt->fetchAll();
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">

    <div class="page-header">
        <h1>Planning de la mission</h1>
        <a href="<?= $role === 'manager' ? 'toutes_missions.php' : 'mes_missions.php' ?>" class="btn btn-secondary">← Retour</a>
    </div>

    <?php if (!$mission) : ?>
        <p class="empty-state">Mission introuvable ou accès non autorisé.</p>
    <?php else : ?>

        <!-- Infos mission -->
        <div class="form-card" style="margin-bottom:24px">
            <h2 style="font-size:18px;color:#2c3e6b;margin-bottom:16px">
                <?= htmlspecialchars($mission['title']) ?>
            </h2>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;font-size:14px">
                <div><strong>Lieu :</strong> <?= htmlspecialchars($mission['location']) ?></div>
                <div><strong>Zone :</strong> <?= $mission['zone_name'] ? htmlspecialchars($mission['zone_name']) : '—' ?></div>
                <div><strong>Coordinateur :</strong> <?= htmlspecialchars($mission['coordinateur_name']) ?></div>
                <div><strong>Début :</strong> <?= date('d/m/Y H:i', strtotime($mission['starts_at'])) ?></div>
                <div><strong>Fin :</strong> <?= date('d/m/Y H:i', strtotime($mission['ends_at'])) ?></div>
                <div><strong>Compétence :</strong> <?= $mission['skill_name'] ? htmlspecialchars($mission['skill_name']) : '—' ?></div>
                <div>
                    <strong>Couverture :</strong>
                    <?= $mission['nb_assigned'] ?> / <?= $mission['required_capacity'] ?>
                    <?php
                    $taux = $mission['required_capacity'] > 0
                        ? round($mission['nb_assigned'] / $mission['required_capacity'] * 100)
                        : 0;
                    ?>
                    <span class="badge badge-<?= $taux >= 100 ? 'confirmed' : 'warning' ?>">
                        <?= $taux ?>%
                    </span>
                </div>
                <div><strong>Statut :</strong> <span class="badge badge-<?= $mission['status'] ?>"><?= $mission['status'] ?></span></div>
            </div>

            <!-- Barre de progression -->
            <div style="margin-top:16px">
                <div style="background:#e0e0e0;border-radius:4px;height:8px;overflow:hidden">
                    <div style="width:<?= min($taux, 100) ?>%;background:<?= $taux >= 100 ? '#28a745' : '#ffc107' ?>;height:100%;transition:width .3s"></div>
                </div>
                <p style="font-size:12px;color:#888;margin-top:4px">
                    <?= $mission['nb_assigned'] ?> bénévole(s) confirmé(s) sur <?= $mission['required_capacity'] ?> requis
                </p>
            </div>
        </div>

        <!-- Liste bénévoles affectés -->
        <h2 style="font-size:16px;margin-bottom:12px;color:#2c3e6b">
            Bénévoles affectés (<?= count($benevoles_affectes) ?>)
        </h2>

        <?php if (empty($benevoles_affectes)) : ?>
            <p class="empty-state">Aucun bénévole affecté pour le moment.</p>
        <?php else : ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Affecté le</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($benevoles_affectes as $b) : ?>
                        <tr>
                            <td><?= htmlspecialchars($b['benevole_name']) ?></td>
                            <td><?= htmlspecialchars($b['benevole_email']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($b['assigned_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>