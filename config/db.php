<?php

function loadEnv(string $path): array
{
    if (!is_file($path)) {
        return [];
    }

    $env = [];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#') || str_starts_with($line, ';')) {
            continue;
        }

        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $env[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
    }

    return $env;
}

$env = loadEnv(__DIR__ . '/../.env');

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
