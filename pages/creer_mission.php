<?php

session_start();
require_once __DIR__ . '/../config/db.php';

// Seul le coordinateur peut créer une mission
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'coordinator') {
    header('Location: ../login.php');
    exit;
}

// Récupération des erreurs et anciennes valeurs si on vient d'un POST raté
$errors   = $_SESSION['errors']   ?? [];
$old_data = $_SESSION['old_data'] ?? [];
unset($_SESSION['errors'], $_SESSION['old_data']);

// Récupération des compétences et zones pour les selects
$skills = $pdo->query("SELECT id, name FROM skill ORDER BY name")->fetchAll();
$zones  = $pdo->query("SELECT id, name FROM zone ORDER BY name")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">

    <div class="page-header">
        <h1>Créer une mission</h1>
        <a href="mes_missions.php" class="btn btn-secondary">← Retour</a>
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

    <form action="../actions/create_mission.php" method="POST" class="form-card">

        <div class="form-group">
            <label for="title">Titre de la mission <span class="required">*</span></label>
            <input
                type="text"
                id="title"
                name="title"
                value="<?= htmlspecialchars($old_data['title'] ?? '') ?>"
                placeholder="ex. Accueil entrée principale"
                required
            >
        </div>

        <div class="form-group">
            <label for="location">Lieu <span class="required">*</span></label>
            <input
                type="text"
                id="location"
                name="location"
                value="<?= htmlspecialchars($old_data['location'] ?? '') ?>"
                placeholder="ex. Porte A, Scène principale..."
                required
            >
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="starts_at">Début <span class="required">*</span></label>
                <input
                    type="datetime-local"
                    id="starts_at"
                    name="starts_at"
                    value="<?= htmlspecialchars($old_data['starts_at'] ?? '') ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="ends_at">Fin <span class="required">*</span></label>
                <input
                    type="datetime-local"
                    id="ends_at"
                    name="ends_at"
                    value="<?= htmlspecialchars($old_data['ends_at'] ?? '') ?>"
                    required
                >
            </div>
        </div>

        <div class="form-group">
            <label for="required_capacity">Capacité (nombre de bénévoles) <span class="required">*</span></label>
            <input
                type="number"
                id="required_capacity"
                name="required_capacity"
                value="<?= htmlspecialchars($old_data['required_capacity'] ?? '1') ?>"
                min="1"
                required
            >
        </div>

        <div class="form-group">
            <label for="zone_id">Zone</label>
            <select id="zone_id" name="zone_id">
                <option value="">— Aucune zone —</option>
                <?php foreach ($zones as $zone) : ?>
                    <option
                        value="<?= $zone['id'] ?>"
                        <?= ($old_data['zone_id'] ?? '') == $zone['id'] ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($zone['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="required_skill_id">Compétence requise</label>
            <select id="required_skill_id" name="required_skill_id">
                <option value="">— Aucune compétence requise —</option>
                <?php foreach ($skills as $skill) : ?>
                    <option
                        value="<?= $skill['id'] ?>"
                        <?= ($old_data['required_skill_id'] ?? '') == $skill['id'] ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($skill['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea
                id="description"
                name="description"
                rows="3"
                placeholder="Informations supplémentaires sur la mission..."
            ><?= htmlspecialchars($old_data['description'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Créer la mission</button>
            <a href="mes_missions.php" class="btn btn-secondary">Annuler</a>
        </div>

    </form>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>