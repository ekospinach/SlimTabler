<?php
/**
 * Shared bootstrap: session, DB connection, auth helpers, and utility functions.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ========================================================================
// Database Helpers
// ========================================================================

/**
 * Get database connection (singleton).
 */
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

/**
 * Ensure the users table exists (idempotent).
 */
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

/**
 * Get all users ordered by creation date.
 */
function getUsers(): array
{
    ensureUsersTable();
    return db()->query('SELECT id, name, email, created_at FROM users ORDER BY created_at DESC')->fetchAll();
}

/**
 * Find user by ID.
 */
function getUserById(int $id): ?array
{
    ensureUsersTable();
    $stmt = db()->prepare('SELECT id, name, email FROM users WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

/**
 * Find user by email.
 */
function getUserByEmail(string $email): ?array
{
    ensureUsersTable();
    $stmt = db()->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    return $stmt->fetch() ?: null;
}

// ========================================================================
// Authentication Helpers
// ========================================================================

/**
 * Check if user is logged in.
 */
function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

/**
 * Get current logged-in user ID.
 */
function getCurrentUserId(): ?int
{
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current logged-in user name.
 */
function getCurrentUserName(): string
{
    return $_SESSION['user_name'] ?? 'Guest';
}

/**
 * Require authentication - redirect to login if not authenticated.
 */
function requireAuth(): void
{
    if (!isLoggedIn()) {
        header('Location: /login');
        exit;
    }
}

// ========================================================================
// Response Helpers
// ========================================================================

/**
 * Create a redirect response.
 */
function redirect(string $url, int $status = 302): \ Psr\Http\Message\ResponseInterface
{
    return (new \Slim\Psr7\Response())
        ->withHeader('Location', $url)
        ->withStatus($status);
}

// ========================================================================
// Flash Message Helpers
// ========================================================================

/**
 * Set a flash message.
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'][$type] = $message;
}

/**
 * Get and clear a flash message.
 */
function getFlash(string $type): ?string
{
    $message = $_SESSION['flash'][$type] ?? null;
    unset($_SESSION['flash'][$type]);
    return $message;
}

/**
 * Check if there's a flash message of given type.
 */
function hasFlash(string $type): bool
{
    return isset($_SESSION['flash'][$type]);
}
