<?php
/**
 * Application Routes
 * 
 * All route definitions are organized here for easy maintenance.
 * The $app (Slim\App) and $renderer (PhpRenderer) are available via 'use' from the parent scope.
 */

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

// ========================================================================
// Helper: Render Dashboard Page
// ========================================================================

/**
 * Render a page within the dashboard layout.
 */
function renderDashboard(
    Response $response,
    PhpRenderer $renderer,
    string $pageTitle,
    string $pageIcon,
    string $pageDescription,
    string $currentPage,
    string $pageTemplate,
    array $pageData = []
): Response {
    return $renderer->render($response, '/layouts/dashboard.php', [
        'pageTitle' => $pageTitle,
        'pageIcon' => $pageIcon,
        'pageDescription' => $pageDescription,
        'currentPage' => $currentPage,
        'pageTemplate' => $pageTemplate,
        'pageData' => $pageData,
    ]);
}

/**
 * Render an error page within the dashboard layout.
 */
function renderDashboardWithError(
    Response $response,
    PhpRenderer $renderer,
    string $pageTitle,
    string $pageIcon,
    string $pageDescription,
    string $currentPage,
    string $pageTemplate,
    string $errorMessage,
    array $extraData = []
): Response {
    $pageData = array_merge([
        'formError' => $errorMessage,
    ], $extraData);
    
    return renderDashboard(
        $response,
        $renderer,
        $pageTitle,
        $pageIcon,
        $pageDescription,
        $currentPage,
        $pageTemplate,
        $pageData
    );
}

// ========================================================================
// Public Routes
// ========================================================================

// Home - redirect to dashboard if logged in, else to login
$app->get('/', function (Request $request, Response $response) {
    return isLoggedIn() ? redirect('/dashboard') : redirect('/login');
});

// Login Page
$app->get('/login', function (Request $request, Response $response) use ($renderer) {
    if (isLoggedIn()) {
        return redirect('/dashboard');
    }
    return $renderer->render($response, '/pages/login.php', ['error' => '']);
});

// Login Handler
$app->post('/login', function (Request $request, Response $response) use ($renderer) {
    $data = (array) $request->getParsedBody();
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    
    $user = getUserByEmail($email);
    
    if (!$user || !password_verify($password, $user['password'])) {
        return $renderer->render($response, '/pages/login.php', [
            'error' => 'Invalid email or password.',
        ]);
    }
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    
    return redirect('/dashboard');
});

// Logout
$app->get('/logout', function (Request $request, Response $response) {
    session_destroy();
    return redirect('/login');
});

// ========================================================================
// Dashboard Routes
// ========================================================================

$app->get('/dashboard', function (Request $request, Response $response) use ($renderer) {
    return renderDashboard(
        $response,
        $renderer,
        'Dashboard',
        'dashboard',
        'Welcome back! Here\'s what\'s happening with your project today.',
        'dashboard',
        '/pages/dashboard.php'
    );
});

$app->get('/tables', function (Request $request, Response $response) use ($renderer) {
    return renderDashboard(
        $response,
        $renderer,
        'Tables',
        'table',
        'Manage your data with sortable, searchable tables.',
        'tables',
        '/pages/tables.php'
    );
});

$app->get('/blank', function (Request $request, Response $response) use ($renderer) {
    return renderDashboard(
        $response,
        $renderer,
        'Blank Page',
        'file-description',
        'Start building your custom page from this blank template.',
        'blank',
        '/pages/blank.php'
    );
});

// ========================================================================
// User Management Routes
// ========================================================================

// List Users
$app->get('/users', function (Request $request, Response $response) use ($renderer) {
    $users = getUsers();
    
    return renderDashboard(
        $response,
        $renderer,
        'User Management',
        'users',
        'Manage application users.',
        'users',
        '/pages/users.php',
        ['users' => $users]
    );
});

// Create User (POST)
$app->post('/users', function (Request $request, Response $response) use ($renderer) {
    $data = (array) $request->getParsedBody();
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    
    // Validation
    $errors = [];
    if ($name === '') {
        $errors[] = 'Name is required.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    
    if (!empty($errors)) {
        $users = getUsers();
        return renderDashboardWithError(
            $response,
            $renderer,
            'User Management',
            'users',
            'Manage application users.',
            'users',
            '/pages/users.php',
            implode(' ', $errors),
            ['users' => $users]
        );
    }
    
    try {
        $stmt = db()->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
        setFlash('success', 'User created successfully.');
    } catch (\PDOException $e) {
        $users = getUsers();
        return renderDashboardWithError(
            $response,
            $renderer,
            'User Management',
            'users',
            'Manage application users.',
            'users',
            '/pages/users.php',
            'Email already registered.',
            ['users' => $users]
        );
    }
    
    return redirect('/users');
});

// Edit User Form
$app->get('/users/{id}/edit', function (Request $request, Response $response, $args) use ($renderer) {
    $user = getUserById((int) $args['id']);
    
    if (!$user) {
        return redirect('/users');
    }
    
    return renderDashboard(
        $response,
        $renderer,
        'Edit User',
        'user-edit',
        'Edit user details.',
        'users',
        '/pages/user-form.php',
        ['user' => $user]
    );
});

// Update User (POST)
$app->post('/users/{id}/update', function (Request $request, Response $response, $args) use ($renderer) {
    $data = (array) $request->getParsedBody();
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $userId = (int) $args['id'];
    
    // Validation
    $errors = [];
    if ($name === '') {
        $errors[] = 'Name is required.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    
    if (!empty($errors)) {
        $user = ['id' => $userId, 'name' => $name, 'email' => $email];
        return renderDashboardWithError(
            $response,
            $renderer,
            'Edit User',
            'user-edit',
            'Edit user details.',
            'users',
            '/pages/user-form.php',
            implode(' ', $errors),
            ['user' => $user]
        );
    }
    
    // Update query
    if ($password !== '') {
        $stmt = db()->prepare('UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?');
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $userId]);
    } else {
        $stmt = db()->prepare('UPDATE users SET name = ?, email = ? WHERE id = ?');
        $stmt->execute([$name, $email, $userId]);
    }
    
    setFlash('success', 'User updated successfully.');
    return redirect('/users');
});

// Delete User (POST)
$app->post('/users/{id}/delete', function (Request $request, Response $response, $args) {
    $stmt = db()->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([(int) $args['id']]);
    
    setFlash('success', 'User deleted successfully.');
    return redirect('/users');
});

// ========================================================================
// Static Asset Handler
// ========================================================================

$app->get('/assets/{file:.*}', function (Request $request, Response $response, $args) {
    // Sanitize file path - only allow alphanumeric, dots, dashes, underscores, slashes
    $rel = preg_replace('#[^a-zA-Z0-9_./-]#', '', $args['file']);
    $path = __DIR__ . '/assets/' . $rel;
    
    // Security: ensure path stays within assets directory
    $realPath = realpath($path);
    $assetsDir = realpath(__DIR__ . '/assets');
    
    if ($realPath === false || $assetsDir === false || strpos($realPath, $assetsDir) !== 0) {
        return $response->withStatus(404);
    }
    
    if (!is_file($realPath)) {
        return $response->withStatus(404);
    }
    
    $mime = mime_content_type($realPath) ?: 'application/octet-stream';
    $response->getBody()->write(file_get_contents($realPath));
    
    return $response
        ->withHeader('Content-Type', $mime)
        ->withHeader('Cache-Control', 'public, max-age=3600');
});

// ========================================================================
// Tabler Asset Downloader (Setup Route)
// ========================================================================

$app->get('/_setup/download-tabler', function (Request $request, Response $response) {
    $log = [];
    $baseUrl = 'https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/';
    $iconsBaseUrl = 'https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.31.0/dist/';
    $baseDir = __DIR__ . '/assets/tabler';
    
    // Ensure directories exist
    foreach (['', '/css', '/js', '/css/fonts'] as $dir) {
        $fullDir = $baseDir . $dir;
        if (!is_dir($fullDir)) {
            mkdir($fullDir, 0755, true);
        }
    }
    
    // Files to download
    $files = [
        [$baseUrl, 'css/tabler.min.css'],
        [$baseUrl, 'css/tabler-flags.min.css'],
        [$baseUrl, 'css/tabler-payments.min.css'],
        [$baseUrl, 'css/tabler-vendors.min.css'],
        [$baseUrl, 'js/tabler.min.js'],
        [$iconsBaseUrl, 'css/tabler-icons.min.css'],
        [$iconsBaseUrl, 'fonts/tabler-icons.woff2', '/css/'],
        [$iconsBaseUrl, 'fonts/tabler-icons.woff', '/css/'],
        [$iconsBaseUrl, 'fonts/tabler-icons.ttf', '/css/'],
    ];
    
    $log[] = '<h3>📥 Downloading Tabler UI Assets v1.4.0</h3><hr>';
    
    foreach ($files as [$cdnBase, $file, $subdir]) {
        $url = $cdnBase . $file;
        $localPath = $baseDir . ($subdir ?? '/') . basename($file);
        $localDir = dirname($localPath);
        
        if (!is_dir($localDir)) {
            mkdir($localDir, 0755, true);
        }
        
        $content = @file_get_contents($url);
        
        if ($content === false && function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 60,
            ]);
            $content = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($content === false || $httpCode !== 200) {
                $log[] = "<li>❌ Failed: {$url} (HTTP {$httpCode})</li>";
                continue;
            }
        } elseif ($content === false) {
            $log[] = "<li>❌ Failed: {$url}</li>";
            continue;
        }
        
        if (file_put_contents($localPath, $content) !== false) {
            $size = round(strlen($content) / 1024);
            $log[] = "<li>✅ Saved: " . basename($localPath) . " ({$size} KB)</li>";
        } else {
            $log[] = "<li>❌ Failed to write: {$localPath}</li>";
        }
    }
    
    $log[] = '<hr><p><strong>✅ Download complete!</strong></p>';
    $log[] = '<p>Assets are now in: <code>public/assets/tabler/</code></p>';
    $log[] = '<p><a href="/dashboard">Go to Dashboard</a></p>';
    
    $html = '<html><head><title>Tabler Download</title>';
    $html .= '<style>body{font-family:sans-serif;max-width:600px;margin:40px auto;line-height:1.6}ul{list-style:none;padding:0}li{padding:4px 0}code{background:#f0f0f0;padding:2px 6px;border-radius:3px}</style>';
    $html .= '</head><body>' . implode("\n", $log) . '</body></html>';
    
    $response->getBody()->write($html);
    return $response;
});
