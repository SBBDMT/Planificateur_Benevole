<?php

session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SESSION['role_name'] !== 'manager' && $_SESSION['role_name'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$stats = [
    'missions' => 0,
    'volunteers' => 0,
    'assignments' => 0,
    'understaffed' => 0,
];

$stats['missions'] = (int) $pdo
    ->query('SELECT COUNT(*) FROM mission')
    ->fetchColumn();

$stats['volunteers'] = (int) $pdo
    ->query('SELECT COUNT(*) FROM volunteer WHERE active = 1')
    ->fetchColumn();

$stats['assignments'] = (int) $pdo
    ->query("SELECT COUNT(*) FROM assignment WHERE status = 'confirmed'")
    ->fetchColumn();

$stats['understaffed'] = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM (
            SELECT m.id
            FROM mission m
            LEFT JOIN assignment a
                ON a.mission_id = m.id
                AND a.status = 'confirmed'
            WHERE m.status IN ('open', 'full')
            GROUP BY m.id, m.required_capacity
            HAVING COUNT(a.id) < m.required_capacity
        ) understaffed
    ")
    ->fetchColumn();

$understaffedQuery = $pdo->query("
    SELECT
        m.id,
        m.title,
        m.location,
        m.starts_at,
        m.ends_at,
        m.required_capacity,
        z.name AS zone_name,
        u.name AS coordinator_name,
        COUNT(a.id) AS confirmed_count
    FROM mission m
    LEFT JOIN zone z ON z.id = m.zone_id
    INNER JOIN user u ON u.id = m.coordinator_id
    LEFT JOIN assignment a
        ON a.mission_id = m.id
        AND a.status = 'confirmed'
    WHERE m.status IN ('open', 'full')
    GROUP BY
        m.id,
        m.title,
        m.location,
        m.starts_at,
        m.ends_at,
        m.required_capacity,
        z.name,
        u.name
    HAVING confirmed_count < m.required_capacity
    ORDER BY m.starts_at ASC
");
$understaffedMissions = $understaffedQuery->fetchAll();

$recentLogs = $pdo
    ->query("
        SELECT
            al.action,
            al.entity_type,
            al.entity_id,
            al.created_at,
            u.name AS user_name
        FROM audit_log al
        LEFT JOIN user u ON u.id = al.user_id
        ORDER BY al.created_at DESC
        LIMIT 8
    ")
    ->fetchAll();

function formatDateTime(string $dateTime): string
{
    return date('d/m/Y H:i', strtotime($dateTime));
}

require_once __DIR__ . '/../includes/header.php';

?>

<main class="container">
    <div class="page-header">
        <div>
            <h1>Tableau de bord responsable</h1>
            <p class="page-subtitle">Vue globale des missions, bénévoles et affectations.</p>
        </div>
    </div>

    <section class="stats-grid">
        <article class="stat-card">
            <span class="stat-label">Missions</span>
            <strong><?= $stats['missions'] ?></strong>
        </article>

        <article class="stat-card">
            <span class="stat-label">Bénévoles actifs</span>
            <strong><?= $stats['volunteers'] ?></strong>
        </article>

        <article class="stat-card">
            <span class="stat-label">Affectations confirmées</span>
            <strong><?= $stats['assignments'] ?></strong>
        </article>

        <article class="stat-card stat-card-warning">
            <span class="stat-label">Missions sous-dotées</span>
            <strong><?= $stats['understaffed'] ?></strong>
        </article>
    </section>

    <section class="dashboard-section">
        <div class="section-header">
            <h2>Missions sous-dotées</h2>
            <a href="toutes_missions.php" class="btn btn-sm">Voir toutes les missions</a>
        </div>

        <?php if (empty($understaffedMissions)) : ?>
            <div class="empty-state">Toutes les missions ouvertes sont couvertes.</div>
        <?php else : ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Mission</th>
                        <th>Zone</th>
                        <th>Coordinateur</th>
                        <th>Créneau</th>
                        <th>Couverture</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($understaffedMissions as $mission) : ?>
                        <tr class="row-warning">
                            <td>
                                <strong><?= htmlspecialchars($mission['title']) ?></strong><br>
                                <span class="muted"><?= htmlspecialchars($mission['location']) ?></span>
                            </td>
                            <td><?= htmlspecialchars($mission['zone_name'] ?? 'Non définie') ?></td>
                            <td><?= htmlspecialchars($mission['coordinator_name']) ?></td>
                            <td>
                                <?= htmlspecialchars(formatDateTime($mission['starts_at'])) ?><br>
                                <span class="muted">à <?= htmlspecialchars(formatDateTime($mission['ends_at'])) ?></span>
                            </td>
                            <td>
                                <span class="badge badge-warning">
                                    <?= (int) $mission['confirmed_count'] ?> / <?= (int) $mission['required_capacity'] ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <section class="dashboard-section">
        <div class="section-header">
            <h2>Activité récente</h2>
            <a href="toutes_affectations.php" class="btn btn-sm">Voir les affectations</a>
        </div>

        <?php if (empty($recentLogs)) : ?>
            <div class="empty-state">Aucune activité enregistrée pour le moment.</div>
        <?php else : ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Utilisateur</th>
                        <th>Action</th>
                        <th>Entité</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentLogs as $log) : ?>
                        <tr>
                            <td><?= htmlspecialchars(formatDateTime($log['created_at'])) ?></td>
                            <td><?= htmlspecialchars($log['user_name'] ?? 'Système') ?></td>
                            <td><?= htmlspecialchars($log['action']) ?></td>
                            <td>
                                <?= htmlspecialchars($log['entity_type']) ?>
                                <?php if ($log['entity_id'] !== null) : ?>
                                    #<?= (int) $log['entity_id'] ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
