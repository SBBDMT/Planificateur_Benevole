<?php

session_start();
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');

// --- Validation de base ---
$errors = [];

if ($email === '') {
    $errors[] = "L'email est obligatoire.";
}

if ($password === '') {
    $errors[] = "Le mot de passe est obligatoire.";
}

if (!empty($errors)) {
    $_SESSION['errors']   = $errors;
    $_SESSION['old_data'] = ['email' => $email];
    header('Location: ../login.php');
    exit;
}

// --- Récupération de l'utilisateur avec son rôle ---
$stmt = $pdo->prepare("
    SELECT u.id, u.name, u.email, u.password, r.name AS role_name
    FROM user u
    INNER JOIN role r ON r.id = u.role_id
    WHERE u.email = :email
    LIMIT 1
");
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

// --- Vérification mot de passe ---
if (!$user || !password_verify($password, $user['password'])) {
    $_SESSION['errors']   = ["Email ou mot de passe incorrect."];
    $_SESSION['old_data'] = ['email' => $email];
    header('Location: ../login.php');
    exit;
}

// --- Création de la session ---
$_SESSION['user_id']   = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_email']= $user['email'];
$_SESSION['role_name'] = $user['role_name'];

// --- Log audit ---
$log = $pdo->prepare("
    INSERT INTO audit_log (user_id, action, entity_type, entity_id)
    VALUES (:uid, 'user.login', 'user', :uid2)
");
$log->execute([':uid' => $user['id'], ':uid2' => $user['id']]);

// --- Redirection selon le rôle ---
switch ($user['role_name']) {
    case 'coordinator':
        header('Location: pages/mes_missions.php');
        break;
    case 'volunteer':
        header('Location: pages/planning.php');
        break;
    case 'manager':
        header('Location: pages/dashboard.php');
        break;
    case 'admin':
        header('Location: pages/dashboard.php');
        break;
    default:
        header('Location: index.php');
        break;
}
exit;