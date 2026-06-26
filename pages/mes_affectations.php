<?php

session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'coordinator') {
    header('Location: ../login.php');
    exit;
}

$errors  = $_SESSION['errors']  ?? [];
$success = $_SESSION['success'] ?? null;
unset($_SESSION['errors'], $_SESSION['success']);

$mission_id = (int) ($_GET['mission_id'] ?? 0);

// Récupération de la mission du coordinateur
$mission = null;
if ($mission_id > 0) {
    $stmt = $pdo->prepare("
        SELECT m.id, m.title, m.location, m.starts_at, m.ends_at, m.required_capacity, m.status,
               COUNT(a.id) AS nb_assigned
        FROM mission m
        LEFT JOIN assignment a ON a.mission_id = m.id AND a.status = 'confirmed'
        WHERE m.id = :mid AND m.coordinator_id = :uid
        GROUP BY m.id
    ");
    $stmt->execute([':mid' => $mission_id, ':uid' => $_SESSION['user_id']]);
    $mission = $stmt->fetch();
}

// Bénévoles actifs pour le select
$benevoles = $pdo->query("
    SELECT v.id, u.name, u.email
    FROM volunteer v
    INNER JOIN user u ON u.id = v.user_id
    WHERE v.active = 1
    ORDER BY u.name ASC
")->fetchAll();

// Affectations de la mission
$affectations = [];
if ($mission_id > 0) {
    $stmt = $pdo->prepare("
        SELECT a.id, a.status, a.created_at,
               u.name AS benevole_name, u.email AS benevole_email
        FROM assignment a
        INNER JOIN volunteer v ON v.id = a.volunteer_id
        INNER JOIN user u ON u.id = v.user_id
        WHERE a.mission_id = :mid
        ORDER BY a.created_at DESC
    ");
    $stmt->execute([':mid' => $mission_id]);
    $affectations = $stmt->fetchAll();
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">

    <div class="page-header">
        <h1>Affectations<?= $mission ? ' — ' . htmlspecialchars($mission['title']) : '' ?></h1>
        <div style="display:flex;gap:10px">
            <a href="audit_log.php" class="btn btn-secondary">📋 Journal</a>
            <a href="mes_missions.php" class="btn btn-secondary">← Retour</a>
        </div>
    </div>

    <?php if ($success) : ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)) : ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $e) : ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!$mission) : ?>
        <p class="empty-state">Mission introuvable ou accès non autorisé.</p>
    <?php else : ?>

        <!-- Infos mission -->
        <div class="form-card" style="margin-bottom:24px">
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;font-size:14px">
                <div><strong>Lieu :</strong> <?= htmlspecialchars($mission['location']) ?></div>
                <div><strong>Début :</strong> <?= date('d/m/Y H:i', strtotime($mission['starts_at'])) ?></div>
                <div><strong>Fin :</strong> <?= date('d/m/Y H:i', strtotime($mission['ends_at'])) ?></div>
                <div>
                    <strong>Couverture :</strong>
                    <?= $mission['nb_assigned'] ?> / <?= $mission['required_capacity'] ?>
                    <?php if ($mission['nb_assigned'] < $mission['required_capacity']) : ?>
                        <span class="badge badge-warning">Sous-dotée</span>
                    <?php else : ?>
                        <span class="badge badge-confirmed">Complète</span>
                    <?php endif; ?>
                </div>
                <div><strong>Statut :</strong> <span class="badge badge-<?= $mission['status'] ?>"><?= $mission['status'] ?></span></div>
            </div>
        </div>

        <!-- Formulaire affectation -->
        <?php if ($mission['nb_assigned'] < $mission['required_capacity']) : ?>
            <form action="../actions/affecter.php" method="POST" class="form-card" style="margin-bottom:24px">
                <h2 style="font-size:16px;margin-bottom:14px;color:#2c3e6b">Affecter un bénévole</h2>
                <input type="hidden" name="mission_id" value="<?= $mission['id'] ?>">
                <div class="form-group">
                    <label for="volunteer_id">Bénévole <span class="required">*</span></label>
                    <select id="volunteer_id" name="volunteer_id" required>
                        <option value="">— Sélectionner un bénévole —</option>
                        <?php foreach ($benevoles as $b) : ?>
                            <option value="<?= $b['id'] ?>">
                                <?= htmlspecialchars($b['name']) ?> — <?= htmlspecialchars($b['email']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Affecter</button>
                </div>
            </form>
        <?php else : ?>
            <div class="alert alert-success">Cette mission est complète — plus d'affectation possible.</div>
        <?php endif; ?>

        <!-- Liste des affectations -->
        <h2 style="font-size:16px;margin-bottom:12px;color:#2c3e6b">Bénévoles affectés</h2>

        <?php if (empty($affectations)) : ?>
            <p class="empty-state">Aucun bénévole affecté pour le moment.</p>
        <?php else : ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Bénévole</th>
                        <th>Email</th>
                        <th>Statut</th>
                        <th>Affecté le</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($affectations as $a) : ?>
                        <tr>
                            <td><?= htmlspecialchars($a['benevole_name']) ?></td>
                            <td><?= htmlspecialchars($a['benevole_email']) ?></td>
                            <td><span class="badge badge-<?= $a['status'] ?>"><?= $a['status'] ?></span></td>
                            <td><?= date('d/m/Y H:i', strtotime($a['created_at'])) ?></td>
                            <td>
                                <?php if ($a['status'] === 'confirmed') : ?>
                                    <form action="../actions/desaffecter.php" method="POST" style="display:inline"
                                          onsubmit="return confirm('Désaffecter ce bénévole ?')">
                                        <input type="hidden" name="assignment_id" value="<?= $a['id'] ?>">
                                        <input type="hidden" name="mission_id" value="<?= $mission_id ?>">
                                        <button type="submit" class="btn btn-sm" style="background:#f8d7da;color:#721c24">
                                            Désaffecter
                                        </button>
                                    </form>
                                <?php else : ?>
                                    —
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>