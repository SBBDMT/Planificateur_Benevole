<?php

session_start();
require_once __DIR__ . '/../config/db.php';

// Seul le coordinateur peut créer une mission
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'coordinator') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/creer_mission.php');
    exit;
}

// Récupération et nettoyage des champs
$title             = trim($_POST['title']             ?? '');
$location          = trim($_POST['location']          ?? '');
$starts_at         = trim($_POST['starts_at']         ?? '');
$ends_at           = trim($_POST['ends_at']           ?? '');
$required_capacity = (int) ($_POST['required_capacity'] ?? 1);
$required_skill_id = !empty($_POST['required_skill_id']) ? (int) $_POST['required_skill_id'] : null;
$zone_id           = !empty($_POST['zone_id'])           ? (int) $_POST['zone_id']           : null;
$description       = trim($_POST['description']       ?? '');

// --- Validation ---
$errors = [];

if ($title === '') {
    $errors[] = "Le titre est obligatoire.";
}

if ($location === '') {
    $errors[] = "Le lieu est obligatoire.";
}

if ($starts_at === '' || $ends_at === '') {
    $errors[] = "Les dates de début et de fin sont obligatoires.";
} elseif ($ends_at <= $starts_at) {
    $errors[] = "La date de fin doit être après la date de début.";
}

if ($required_capacity < 1) {
    $errors[] = "La capacité doit être d'au moins 1.";
}

// Si erreurs → on repart sur le formulaire avec les erreurs en session
if (!empty($errors)) {
    $_SESSION['errors']   = $errors;
    $_SESSION['old_data'] = $_POST;
    header('Location: ../pages/creer_mission.php');
    exit;
}

// --- Insertion en base ---
$sql = "INSERT INTO mission 
            (title, description, location, zone_id, coordinator_id, starts_at, ends_at, required_capacity, required_skill_id, status)
        VALUES 
            (:title, :description, :location, :zone_id, :coordinator_id, :starts_at, :ends_at, :required_capacity, :required_skill_id, 'open')";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':title'             => $title,
    ':description'       => $description ?: null,
    ':location'          => $location,
    ':zone_id'           => $zone_id,
    ':coordinator_id'    => $_SESSION['user_id'],
    ':starts_at'         => $starts_at,
    ':ends_at'           => $ends_at,
    ':required_capacity' => $required_capacity,
    ':required_skill_id' => $required_skill_id,
]);

$mission_id = $pdo->lastInsertId();

// --- Log audit ---
$log = $pdo->prepare("INSERT INTO audit_log (user_id, action, entity_type, entity_id) VALUES (:uid, 'mission.created', 'mission', :eid)");
$log->execute([':uid' => $_SESSION['user_id'], ':eid' => $mission_id]);

// Succès
$_SESSION['success'] = "Mission \"$title\" créée avec succès.";
header('Location: ../pages/mes_missions.php');
exit;