<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/bootstrap.php';

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add Clockwork middleware for deep profiling & telemetry
$app->add(new \Clockwork\Support\Slim\ClockworkMiddleware($app, __DIR__ . '/../storage/clockwork'));

// Add Structured Logging middleware for correlation ID & JSON event logging
$app->add(new \Slim\Middleware\StructuredLoggingMiddleware());

// Create PHP-View renderer
$renderer = new PhpRenderer(__DIR__ . '/../views');
$renderer->setAttributes([
    'basePath' => '',
]);

// ===== Routes =====

// Home - redirect to dashboard if logged in, else to login
$app->get('/', function (Request $request, Response $response) {
    if (isLoggedIn()) {
        return $response->withHeader('Location', '/dashboard')->withStatus(302);
    }
    return $response->withHeader('Location', '/login')->withStatus(302);
});

// ===== Auth Routes =====

$app->get('/login', function (Request $request, Response $response) use ($renderer) {
    if (isLoggedIn()) {
        return $response->withHeader('Location', '/dashboard')->withStatus(302);
    }
    return $renderer->render($response, '/pages/login.php', ['error' => '']);
});

$app->post('/login', function (Request $request, Response $response) {
    ensureUsersTable();
    $data = (array) $request->getParsedBody();
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    $stmt = db()->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        return $renderer->render($response, '/pages/login.php', ['error' => 'Invalid email or password.']);
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    return $response->withHeader('Location', '/dashboard')->withStatus(302);
});

$app->get('/logout', function (Request $request, Response $response) {
    session_destroy();
    return $response->withHeader('Location', '/login')->withStatus(302);
});

// ===== Protected Routes (require login) =====
$app->add(function ($request, $handler) {
    $path = $request->getUri()->getPath();
    $public = ['/login', '/hello'];
    if (isLoggedIn() || in_array($path, $public, true) || strpos($path, '/assets/') === 0) {
        return $handler->handle($request);
    }
    return (new \Slim\Psr7\Response())->withHeader('Location', '/login')->withStatus(302);
});


// ===== Dashboard Routes =====

$app->get('/dashboard', function (Request $request, Response $response) use ($renderer) {
    return $renderer->render($response, '/layouts/dashboard.php', [
        'pageTitle'       => 'Dashboard',
        'pageIcon'        => 'dashboard',
        'pageDescription' => 'Welcome back! Here\'s what\'s happening with your project today.',
        'currentPage'     => 'dashboard',
        'pageTemplate' => '/pages/dashboard.php',
        'pageData' => [],
    ]);
});

$app->get('/tables', function (Request $request, Response $response) use ($renderer) {
    return $renderer->render($response, '/layouts/dashboard.php', [
        'pageTitle'       => 'Tables',
        'pageIcon'        => 'table',
        'pageDescription' => 'Manage your data with sortable, searchable tables.',
        'currentPage'     => 'tables',
        'pageTemplate' => '/pages/tables.php',
        'pageData' => [],
    ]);
});

$app->get('/blank', function (Request $request, Response $response) use ($renderer) {
    return $renderer->render($response, '/layouts/dashboard.php', [
        'pageTitle'       => 'Blank Page',
        'pageIcon'        => 'file-description',
        'pageDescription' => 'Start building your custom page from this blank template.',
        'currentPage'     => 'blank',
        'pageTemplate' => '/pages/blank.php',
        'pageData' => [],
    ]);
});

// ===== Tabler Asset Downloader Route =====
// Access /_setup/download-tabler to download all Tabler UI assets locally
$app->get('/_setup/download-tabler', function (Request $request, Response $response) {
    $baseDir = __DIR__ . '/assets/tabler';
    $dirs = [
        $baseDir,
        $baseDir . '/css',
        $baseDir . '/js',
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    function downloadFile($url, $localPath, &$log) {
        $content = @file_get_contents($url);
        if ($content === false) {
            // Try curl fallback
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            $content = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($content === false || $httpCode !== 200) {
                $log[] = "❌ Failed: $url (HTTP $httpCode)";
                return false;
            }
        }
        if (file_put_contents($localPath, $content) !== false) {
            $log[] = "✅ Saved: " . basename($localPath) . " (" . round(strlen($content) / 1024) . " KB)";
            return true;
        }
        $log[] = "❌ Failed to write: $localPath";
        return false;
    }

    $log = [];
    $log[] = '<h3>📥 Downloading Tabler UI Assets v1.4.0</h3>';
    $log[] = '<hr>';

    // Tabler Core files
    $baseUrl = 'https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/';
    $files = [
        'css/tabler.min.css',
        'css/tabler-flags.min.css',
        'css/tabler-payments.min.css',
        'css/tabler-vendors.min.css',
        'js/tabler.min.js',
    ];

    $log[] = '<h4>Tabler Core</h4><ul>';
    foreach ($files as $file) {
        $url = $baseUrl . $file;
        $localPath = $baseDir . '/' . $file;
        $localDir = dirname($localPath);
        if (!is_dir($localDir)) {
            mkdir($localDir, 0755, true);
        }
        downloadFile($url, $localPath, $log);
    }
    $log[] = '</ul>';

    // Tabler Icons
    $log[] = '<h4>Tabler Icons</h4><ul>';
    $iconsBaseUrl = 'https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.31.0/dist/';
    $iconFiles = [
        'tabler-icons.min.css',
    ];
    foreach ($iconFiles as $file) {
        $url = $iconsBaseUrl . $file;
        $localPath = $baseDir . '/css/' . $file;
        downloadFile($url, $localPath, $log);
    }
    $log[] = '</ul>';

    // Tabler Icons webfont files (referenced relatively by tabler-icons.min.css)
    $log[] = '<h4>Tabler Icons Fonts</h4><ul>';
    $iconFonts = [
        'fonts/tabler-icons.woff2',
        'fonts/tabler-icons.woff',
        'fonts/tabler-icons.ttf',
    ];
    foreach ($iconFonts as $file) {
        $url = $iconsBaseUrl . $file;
        $localPath = $baseDir . '/css/' . $file;
        downloadFile($url, $localPath, $log);
    }
    $log[] = '</ul>';

    $log[] = '<hr><p><strong>✅ Download complete!</strong></p>';
    $log[] = '<p>Assets are now in: <code>public/assets/tabler/</code></p>';
    $log[] = '<p><a href="/dashboard">Go to Dashboard</a></p>';

    $response->getBody()->write('<html><head><title>Tabler Download</title><style>body{font-family:sans-serif;max-width:600px;margin:40px auto;line-height:1.6}ul{list-style:none;padding:0}li{padding:4px 0}code{background:#f0f0f0;padding:2px 6px;border-radius:3px}</style></head><body>' . implode("\n", $log) . '</body></html>');
    return $response;
});

// ===== User Management Routes =====

$app->get('/users', function (Request $request, Response $response) use ($renderer) {
    ensureUsersTable();
    $users = db()->query('SELECT id, name, email, created_at FROM users ORDER BY created_at DESC')->fetchAll();
    return $renderer->render($response, '/layouts/dashboard.php', [
        'pageTitle'       => 'User Management',
        'pageIcon'        => 'users',
        'pageDescription' => 'Manage application users.',
        'currentPage'     => 'users',
        'pageTemplate'    => '/pages/users.php',
        'users'           => $users,
        'pageData'        => ['users' => $users],
    ]);
});

$app->post('/users', function (Request $request, Response $response) use ($renderer) {
    $data = (array) $request->getParsedBody();
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if ($name === '' || $email === '' || strlen($password) < 6) {
        $users = db()->query('SELECT id, name, email, created_at FROM users ORDER BY created_at DESC')->fetchAll();
        return $renderer->render($response, '/layouts/dashboard.php', [
            'pageTitle' => 'User Management', 'pageIcon' => 'users',
            'pageDescription' => 'Manage application users.', 'currentPage' => 'users',
            'pageTemplate' => '/pages/users.php', 'users' => $users,
            'formError' => 'Name, valid email, and password (min 6 chars) are required.',
            'pageData' => ['users' => $users, 'formError' => 'Name, valid email, and password (min 6 chars) are required.'],
        ]);
    }

    try {
        $stmt = db()->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
    } catch (\PDOException $e) {
        $users = db()->query('SELECT id, name, email, created_at FROM users ORDER BY created_at DESC')->fetchAll();
        return $renderer->render($response, '/layouts/dashboard.php', [
            'pageTitle' => 'User Management', 'pageIcon' => 'users',
            'pageDescription' => 'Manage application users.', 'currentPage' => 'users',
            'pageTemplate' => '/pages/users.php', 'users' => $users,
            'formError' => 'Email already registered.',
            'pageData' => ['users' => $users, 'formError' => 'Email already registered.'],
        ]);
    }
    return $response->withHeader('Location', '/users')->withStatus(302);
});

$app->get('/users/{id}/edit', function (Request $request, Response $response, $args) use ($renderer) {
    $stmt = db()->prepare('SELECT id, name, email FROM users WHERE id = ?');
    $stmt->execute([$args['id']]);
    $user = $stmt->fetch();
    if (!$user) {
        return $response->withHeader('Location', '/users')->withStatus(302);
    }
    return $renderer->render($response, '/layouts/dashboard.php', [
        'pageTitle' => 'Edit User', 'pageIcon' => 'user-edit',
        'pageDescription' => 'Edit user details.', 'currentPage' => 'users',
        'pageTemplate' => '/pages/user-form.php', 'user' => $user,
        'pageData' => ['user' => $user],
    ]);
});

$app->post('/users/{id}/update', function (Request $request, Response $response, $args) {
    $data = (array) $request->getParsedBody();
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if ($name === '' || $email === '') {
        $user = ['id' => $args['id'], 'name' => $name, 'email' => $email];
        return $renderer->render($response, '/layouts/dashboard.php', [
            'pageTitle' => 'Edit User', 'pageIcon' => 'user-edit',
            'pageDescription' => 'Edit user details.', 'currentPage' => 'users',
            'pageTemplate' => '/pages/user-form.php',
            'user' => $user,
            'formError' => 'Name and email are required.',
            'pageData' => ['user' => $user, 'formError' => 'Name and email are required.'],
        ]);
    }

    if ($password !== '') {
        $stmt = db()->prepare('UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?');
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $args['id']]);
    } else {
        $stmt = db()->prepare('UPDATE users SET name = ?, email = ? WHERE id = ?');
        $stmt->execute([$name, $email, $args['id']]);
    }
    return $response->withHeader('Location', '/users')->withStatus(302);
});

$app->post('/users/{id}/delete', function (Request $request, Response $response, $args) {
    $stmt = db()->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$args['id']]);
    return $response->withHeader('Location', '/users')->withStatus(302);
});

// ===== Static Asset Handler =====
// Serves files from public/assets when the web server docroot is the project
// root instead of public/. Path is locked inside public/assets (no traversal).
$app->get('/assets/{file:.*}', function (Request $request, Response $response, $args) {
    $rel = preg_replace('#[^a-zA-Z0-9_./-]#', '', $args['file']);
    $path = __DIR__ . '/assets/' . $rel;
    if (!is_file($path) || strpos(realpath($path), realpath(__DIR__ . '/assets')) !== 0) {
        return $response->withStatus(404);
    }
    $mime = mime_content_type($path) ?: 'application/octet-stream';
    $response->getBody()->write(file_get_contents($path));
    return $response->withHeader('Content-Type', $mime)
        ->withHeader('Cache-Control', 'public, max-age=3600');
});

$app->run();
