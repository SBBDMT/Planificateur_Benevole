<?php

session_start();
require_once __DIR__ . '/../config/db.php';

// Seul le coordinateur peut affecter
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'coordinator') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/mes_affectations.php');
    exit;
}

// Récupération des champs
$mission_id   = (int) ($_POST['mission_id']   ?? 0);
$volunteer_id = (int) ($_POST['volunteer_id'] ?? 0);

// --- Validation de base ---
$errors = [];

if ($mission_id === 0 || $volunteer_id === 0) {
    $errors[] = "Mission et bénévole sont obligatoires.";
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: ../pages/mes_affectations.php?mission_id=$mission_id");
    exit;
}

// --- Récupération de la mission ---
// On vérifie aussi que c'est bien une mission du coordinateur connecté
$stmt = $pdo->prepare("
    SELECT id, title, starts_at, ends_at, required_capacity, coordinator_id
    FROM mission
    WHERE id = :mid
");
$stmt->execute([':mid' => $mission_id]);
$mission = $stmt->fetch();

if (!$mission) {
    $_SESSION['errors'] = ["Mission introuvable."];
    header('Location: ../pages/mes_missions.php');
    exit;
}

if ($mission['coordinator_id'] !== $_SESSION['user_id']) {
    $_SESSION['errors'] = ["Vous ne pouvez pas affecter sur une mission qui ne vous appartient pas."];
    header('Location: ../pages/mes_missions.php');
    exit;
}

// --- US3-C : Vérification capacité ---
$nb_assigned = $pdo->prepare("
    SELECT COUNT(*) FROM assignment
    WHERE mission_id = :mid
      AND status = 'confirmed'
");
$nb_assigned->execute([':mid' => $mission_id]);
$count = (int) $nb_assigned->fetchColumn();

if ($count >= $mission['required_capacity']) {
    $_SESSION['errors'] = ["La mission \"{$mission['title']}\" est complète ({$count}/{$mission['required_capacity']} bénévoles)."];
    header("Location: ../pages/mes_affectations.php?mission_id=$mission_id");
    exit;
}

// --- Vérification que le bénévole n'est pas déjà affecté à cette mission ---
$already = $pdo->prepare("
    SELECT id FROM assignment
    WHERE mission_id   = :mid
      AND volunteer_id = :vid
      AND status != 'cancelled'
");
$already->execute([':mid' => $mission_id, ':vid' => $volunteer_id]);

if ($already->fetch()) {
    $_SESSION['errors'] = ["Ce bénévole est déjà affecté à cette mission."];
    header("Location: ../pages/mes_affectations.php?mission_id=$mission_id");
    exit;
}

// --- US3-A : Vérification disponibilité ---
// Le bénévole doit avoir une dispo qui couvre TOUT le créneau de la mission
$dispo = $pdo->prepare("
    SELECT id FROM availability
    WHERE volunteer_id = :vid
      AND starts_at <= :mission_start
      AND ends_at   >= :mission_end
    LIMIT 1
");
$dispo->execute([
    ':vid'           => $volunteer_id,
    ':mission_start' => $mission['starts_at'],
    ':mission_end'   => $mission['ends_at'],
]);

if (!$dispo->fetch()) {
    $_SESSION['errors'] = ["Ce bénévole n'est pas disponible sur tout le créneau de cette mission."];
    header("Location: ../pages/mes_affectations.php?mission_id=$mission_id");
    exit;
}

// --- US3-B : Vérification conflit horaire ---
// Le bénévole ne doit pas être déjà affecté à une mission qui chevauche ce créneau
$conflit = $pdo->prepare("
    SELECT m.title FROM assignment a
    INNER JOIN mission m ON m.id = a.mission_id
    WHERE a.volunteer_id = :vid
      AND a.status       = 'confirmed'
      AND a.mission_id  != :mid
      AND m.starts_at    < :mission_end
      AND m.ends_at      > :mission_start
    LIMIT 1
");
$conflit->execute([
    ':vid'           => $volunteer_id,
    ':mid'           => $mission_id,
    ':mission_start' => $mission['starts_at'],
    ':mission_end'   => $mission['ends_at'],
]);
$mission_en_conflit = $conflit->fetch();

if ($mission_en_conflit) {
    $_SESSION['errors'] = ["Conflit horaire : ce bénévole est déjà affecté à la mission \"{$mission_en_conflit['title']}\" sur ce créneau."];
    header("Location: ../pages/mes_affectations.php?mission_id=$mission_id");
    exit;
}

// --- Toutes les vérifications sont OK → INSERT ---
$insert = $pdo->prepare("
    INSERT INTO assignment (mission_id, volunteer_id, assigned_by, status)
    VALUES (:mid, :vid, :assigned_by, 'confirmed')
");
$insert->execute([
    ':mid'         => $mission_id,
    ':vid'         => $volunteer_id,
    ':assigned_by' => $_SESSION['user_id'],
]);

$assignment_id = $pdo->lastInsertId();

// Mettre à jour le statut de la mission si elle est maintenant complète
$nb_after = (int) $pdo->prepare("
    SELECT COUNT(*) FROM assignment
    WHERE mission_id = :mid AND status = 'confirmed'
")->execute([':mid' => $mission_id]) ? $count + 1 : $count;

if (($count + 1) >= $mission['required_capacity']) {
    $pdo->prepare("UPDATE mission SET status = 'full' WHERE id = :mid")
        ->execute([':mid' => $mission_id]);
}

// --- Log audit ---
$log = $pdo->prepare("
    INSERT INTO audit_log (user_id, action, entity_type, entity_id)
    VALUES (:uid, 'assignment.created', 'assignment', :eid)
");
$log->execute([':uid' => $_SESSION['user_id'], ':eid' => $assignment_id]);

$_SESSION['success'] = "Bénévole affecté avec succès à la mission \"{$mission['title']}\".";
header("Location: ../pages/mes_affectations.php?mission_id=$mission_id");
exit;