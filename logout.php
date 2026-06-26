<?php

session_start();

// Log audit avant de détruire la session
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/config/db.php';

    $log = $pdo->prepare("
        INSERT INTO audit_log (user_id, action, entity_type, entity_id)
        VALUES (:uid, 'user.logout', 'user', :uid2)
    ");
    $log->execute([':uid' => $_SESSION['user_id'], ':uid2' => $_SESSION['user_id']]);
}

// Destruction complète de la session
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

header('Location: login.php');
exit;