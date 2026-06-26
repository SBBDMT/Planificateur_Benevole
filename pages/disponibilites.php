<?php
require_once '../config/db.php';
require_once '../includes/header.php';

$userId = $_SESSION['user_id'];

// Récupération du bénévole
$stmt = $pdo->prepare("
    SELECT id
    FROM volunteer
    WHERE user_id = ?
");
$stmt->execute([$userId]);

$volunteer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$volunteer) {
    die("Profil bénévole introuvable.");
}

$volunteerId = $volunteer['id'];

// Liste des disponibilités
$stmt = $pdo->prepare("
    SELECT *
    FROM availability
    WHERE volunteer_id = ?
    ORDER BY starts_at
");
$stmt->execute([$volunteerId]);

$disponibilites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Mes disponibilités</h2>

<form action="../actions/save_disponibilite.php" method="POST">

    <label>Début</label><br>
    <input type="datetime-local" name="starts_at" required><br><br>

    <label>Fin</label><br>
    <input type="datetime-local" name="ends_at" required><br><br>

    <button type="submit">
        Enregistrer
    </button>

</form>

<hr>

<h3>Disponibilités enregistrées</h3>

<?php if(empty($disponibilites)): ?>

    <p>Aucune disponibilité enregistrée.</p>

<?php else: ?>

<table border="1" cellpadding="8">

<tr>
    <th>Début</th>
    <th>Fin</th>
</tr>

<?php foreach($disponibilites as $d): ?>

<tr>

<td><?= date('d/m/Y H:i', strtotime($d['starts_at'])) ?></td>

<td><?= date('d/m/Y H:i', strtotime($d['ends_at'])) ?></td>

</tr>

<?php endforeach; ?>

</table>

<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>