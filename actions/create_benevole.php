<?php

session_start();
require_once __DIR__ . '/../config/db.php';

// Seul le coordinateur peut créer un bénévole
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'coordinator') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/creer_benevole.php');
    exit;
}

// Récupération et nettoyage
$name     = trim($_POST['name']     ?? '');
$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');
$phone    = trim($_POST['phone']    ?? '');

// --- Validation ---
$errors = [];

if ($name === '') {
    $errors[] = "Le nom est obligatoire.";
}

if ($email === '') {
    $errors[] = "L'email est obligatoire.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "L'email n'est pas valide.";
} else {
    // Vérifier que l'email n'est pas déjà utilisé
    $check = $pdo->prepare("SELECT id FROM user WHERE email = :email");
    $check->execute([':email' => $email]);
    if ($check->fetch()) {
        $errors[] = "Cet email est déjà utilisé.";
    }
}

if ($password === '') {
    $errors[] = "Le mot de passe est obligatoire.";
} elseif (strlen($password) < 6) {
    $errors[] = "Le mot de passe doit faire au moins 6 caractères.";
}

if (!empty($errors)) {
    $_SESSION['errors']   = $errors;
    $_SESSION['old_data'] = $_POST;
    header('Location: ../pages/creer_benevole.php');
    exit;
}

// --- Récupération du role_id volunteer ---
$role = $pdo->prepare("SELECT id FROM role WHERE name = 'volunteer'");
$role->execute();
$role_id = $role->fetchColumn();

// --- Insertion dans user ---
$stmt = $pdo->prepare("
    INSERT INTO user (name, email, password, role_id)
    VALUES (:name, :email, :password, :role_id)
");
$stmt->execute([
    ':name'     => $name,
    ':email'    => $email,
    ':password' => password_hash($password, PASSWORD_BCRYPT),
    ':role_id'  => $role_id,
]);

$user_id = $pdo->lastInsertId();

// --- Insertion dans volunteer ---
$vol = $pdo->prepare("
    INSERT INTO volunteer (user_id, phone, active)
    VALUES (:user_id, :phone, 1)
");
$vol->execute([
    ':user_id' => $user_id,
    ':phone'   => $phone ?: null,
]);

$volunteer_id = $pdo->lastInsertId();

// --- Log audit ---
$log = $pdo->prepare("
    INSERT INTO audit_log (user_id, action, entity_type, entity_id)
    VALUES (:uid, 'volunteer.created', 'volunteer', :eid)
");
$log->execute([':uid' => $_SESSION['user_id'], ':eid' => $volunteer_id]);

$_SESSION['success'] = "Bénévole \"$name\" créé avec succès.";
header('Location: ../pages/benevoles.php');
exit;