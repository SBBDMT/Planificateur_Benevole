<?php

session_start();

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old_data'] ?? [];

unset($_SESSION['errors']);
unset($_SESSION['old_data']);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Planificateur de bénévoles</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page">

    <main class="login-container">
        <section class="login-box" aria-labelledby="login-title">
            <div class="login-header">
                <p class="login-kicker">Planificateur de bénévoles</p>
                <h1 id="login-title">Connexion</h1>
            </div>

            <?php if (!empty($errors)) : ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error) : ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="actions/login.php" method="POST" class="login-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                        autocomplete="email"
                        required>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        autocomplete="current-password"
                        required>
                </div>

                <button type="submit" class="btn btn-primary login-submit">
                    Se connecter
                </button>
            </form>
        </section>
    </main>

</body>
</html>
