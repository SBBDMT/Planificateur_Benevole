<?php

session_start();
require_once __DIR__ . '/../config/db.php';

// Seul le coordinateur peut désaffecter
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'coordinator') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/mes_missions.php');
    exit;
}

$assignment_id = (int) ($_POST['assignment_id'] ?? 0);
$mission_id    = (int) ($_POST['mission_id']    ?? 0);

if ($assignment_id === 0 || $mission_id === 0) {
    $_SESSION['errors'] = ["Données manquantes."];
    header("Location: ../pages/mes_affectations.php?mission_id=$mission_id");
    exit;
}

// Vérification que l'affectation appartient bien à une mission du coordinateur connecté
$check = $pdo->prepare("
    SELECT a.id, a.volunteer_id, a.status, m.title, m.coordinator_id
    FROM assignment a
    INNER JOIN mission m ON m.id = a.mission_id
    WHERE a.id = :aid AND a.mission_id = :mid
");
$check->execute([':aid' => $assignment_id, ':mid' => $mission_id]);
$assignment = $check->fetch();

if (!$assignment) {
    $_SESSION['errors'] = ["Affectation introuvable."];
    header("Location: ../pages/mes_affectations.php?mission_id=$mission_id");
    exit;
}

if ($assignment['coordinator_id'] !== $_SESSION['user_id']) {
    $_SESSION['errors'] = ["Vous ne pouvez pas modifier une affectation qui ne vous appartient pas."];
    header("Location: ../pages/mes_affectations.php?mission_id=$mission_id");
    exit;
}

if ($assignment['status'] === 'cancelled') {
    $_SESSION['errors'] = ["Cette affectation est déjà annulée."];
    header("Location: ../pages/mes_affectations.php?mission_id=$mission_id");
    exit;
}

// --- Désaffectation ---
$stmt = $pdo->prepare("
    UPDATE assignment SET status = 'cancelled' WHERE id = :aid
");
$stmt->execute([':aid' => $assignment_id]);

// Repasser la mission en 'open' si elle était 'full'
$pdo->prepare("
    UPDATE mission SET status = 'open'
    WHERE id = :mid AND status = 'full'
")->execute([':mid' => $mission_id]);

// --- Log audit US3-D ---
$log = $pdo->prepare("
    INSERT INTO audit_log (user_id, action, entity_type, entity_id)
    VALUES (:uid, 'assignment.cancelled', 'assignment', :eid)
");
$log->execute([':uid' => $_SESSION['user_id'], ':eid' => $assignment_id]);

$_SESSION['success'] = "Bénévole désaffecté de la mission \"{$assignment['title']}\".";
header("Location: ../pages/mes_affectations.php?mission_id=$mission_id");
exit;