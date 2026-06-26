<?php

session_start();

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old_data'] ?? [];

unset($_SESSION['errors']);
unset($_SESSION['old_data']);

require_once 'includes/header.php';

?>

<main class="login-container">

    <div class="login-box">

        <h2>Connexion</h2>

        <?php foreach($errors as $error): ?>

            <div class="error">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endforeach; ?>

        <form action="actions/login.php" method="POST">

            <label>Email</label>

            <input
                type="email"
                name="email"
                value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                required>

            <label>Mot de passe</label>

            <input
                type="password"
                name="password"
                required>

            <button type="submit">
                Se connecter
            </button>

        </form>

    </div>

</main>

<?php
require_once 'includes/footer.php';
?>