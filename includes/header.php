<?php
// Vérification session — si pas connecté, renvoi sur login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role_name'] ?? '';
$name = $_SESSION['user_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planificateur de bénévoles</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<nav>
    <span class="nav-brand">🎪 Planificateur bénévoles</span>

    <ul>
        <?php if ($role === 'volunteer') : ?>
            <li><a href="../pages/planning.php">Mon planning</a></li>
            <li><a href="../pages/disponibilites.php">Mes disponibilités</a></li>

        <?php elseif ($role === 'coordinator') : ?>
            <li><a href="../pages/mes_missions.php">Mes missions</a></li>
            <li><a href="../pages/mes_affectations.php">Mes affectations</a></li>
            <li><a href="../pages/creer_benevole.php">Créer un bénévole</a></li>

        <?php elseif ($role === 'manager') : ?>
            <li><a href="../pages/dashboard.php">Tableau de bord</a></li>
            <li><a href="../pages/toutes_missions.php">Toutes les missions</a></li>
            <li><a href="../pages/toutes_affectations.php">Toutes les affectations</a></li>
        <?php endif; ?>
    </ul>

    <span class="nav-user">
        <?= htmlspecialchars($name) ?> (<?= htmlspecialchars($role) ?>)
        — <a href="../logout.php" style="color:#f08080">Déconnexion</a>
    </span>
</nav>