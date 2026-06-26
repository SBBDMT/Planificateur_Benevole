<?php

// Chargement du .env
$env = parse_ini_file(__DIR__ . '/../.env');

$host = $env['DB_HOST']     ?? 'localhost';
$port = $env['DB_PORT']     ?? '3306';
$db   = $env['DB_NAME']     ?? 'planificateur_benevoles';
$user = $env['DB_USER']     ?? 'root';
$pass = $env['DB_PASS']     ?? '';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}