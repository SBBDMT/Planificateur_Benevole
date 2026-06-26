<?php

session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'coordinator') {
    header('Location: ../login.php');
    exit;
}

$errors   = $_SESSION['errors']   ?? [];
$old_data = $_SESSION['old_data'] ?? [];
unset($_SESSION['errors'], $_SESSION['old_data']);

$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">

    <div class="page-header">
        <h1>Créer un bénévole</h1>
        <a href="mes_missions.php" class="btn btn-secondary">← Retour</a>
    </div>

    <?php if ($success) : ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)) : ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error) : ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="../actions/create_benevole.php" method="POST" class="form-card">

        <div class="form-group">
            <label for="name">Nom complet <span class="required">*</span></label>
            <input
                type="text"
                id="name"
                name="name"
                value="<?= htmlspecialchars($old_data['name'] ?? '') ?>"
                placeholder="ex. Jean Dupont"
                required
            >
        </div>

        <div class="form-group">
            <label for="email">Email <span class="required">*</span></label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?= htmlspecialchars($old_data['email'] ?? '') ?>"
                placeholder="ex. jean.dupont@email.fr"
                required
            >
        </div>

        <div class="form-group">
            <label for="password">Mot de passe <span class="required">*</span></label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Minimum 6 caractères"
                required
            >
        </div>

        <div class="form-group">
            <label for="phone">Téléphone</label>
            <input
                type="text"
                id="phone"
                name="phone"
                value="<?= htmlspecialchars($old_data['phone'] ?? '') ?>"
                placeholder="ex. 06 12 34 56 78"
            >
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Créer le bénévole</button>
            <a href="benevoles.php" class="btn btn-secondary">Annuler</a>
        </div>

    </form>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>