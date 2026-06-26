<?php

require_once '../config/db.php';

session_start();

$userId = $_SESSION['user_id'];

$starts = $_POST['starts_at'];
$ends = $_POST['ends_at'];

// Vérification simple

if(strtotime($starts) >= strtotime($ends))
{
    die("La date de fin doit être après la date de début.");
}

// Récupération du bénévole

$stmt = $pdo->prepare("
    SELECT id
    FROM volunteer
    WHERE user_id = ?
");

$stmt->execute([$userId]);

$volunteer = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$volunteer)
{
    die("Bénévole introuvable.");
}

$volunteerId = $volunteer['id'];

// Insertion

$stmt = $pdo->prepare("
    INSERT INTO availability
    (
        volunteer_id,
        starts_at,
        ends_at
    )
    VALUES
    (?, ?, ?)
");

$stmt->execute([
    $volunteerId,
    $starts,
    $ends
]);

header("Location: ../pages/disponibilites.php");
exit;