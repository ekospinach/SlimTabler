<?php
// Shared bootstrap: session, DB connection, auth helpers.
// ponytail: kept in one file, no DI container needed for this scope.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// MySQL connection: db "slim" on localhost:3306
// ponytail: credentials inline; move to env if this goes beyond local dev.
function db(): PDO
{
    static $pdo;
    if ($pdo === null) {
        $dsn = 'mysql:host=127.0.0.1;port=3306;dbname=slim;charset=utf8mb4';
        $pdo = new PDO($dsn, 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

// Ensure the users table exists (idempotent).
function ensureUsersTable(): void
{
    db()->exec('
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(120) NOT NULL,
            email VARCHAR(191) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ');
}
