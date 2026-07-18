<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;

require __DIR__ . '/../vendor/autoload.php';

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

// Home - Landing page
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write('
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Slim Framework + Tabler UI</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                    background: #111827; color: #e5e7eb;
                    display: flex; align-items: center; justify-content: center;
                    min-height: 100vh; text-align: center;
                }
                .container { max-width: 640px; padding: 2rem; }
                .logo {
                    width: 80px; height: 80px;
                    background: linear-gradient(135deg, #066fd1, #4299e1);
                    border-radius: 20px;
                    display: inline-flex; align-items: center; justify-content: center;
                    font-size: 2.5rem; color: #fff; margin-bottom: 1.5rem;
                }
                h1 { font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem; }
                .subtitle { color: #9ca3af; font-size: 1.125rem; margin-bottom: 2rem; }
                .links { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
                .links a {
                    display: inline-flex; align-items: center; gap: 0.5rem;
                    padding: 0.75rem 1.5rem; border-radius: 8px;
                    text-decoration: none; font-weight: 500; transition: all 0.2s;
                }
                .btn-primary { background: #066fd1; color: #fff; }
                .btn-primary:hover { background: #0558a8; transform: translateY(-2px); }
                .btn-secondary { background: #1f2937; color: #e5e7eb; border: 1px solid #374151; }
                .btn-secondary:hover { background: #374151; transform: translateY(-2px); }
                .features {
                    display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;
                    margin-top: 3rem; text-align: left;
                }
                .feature {
                    background: #1f2937; padding: 1.25rem; border-radius: 10px;
                    border: 1px solid #374151;
                }
                .feature h3 { font-size: 0.875rem; margin-bottom: 0.5rem; color: #066fd1; }
                .feature p { font-size: 0.8rem; color: #9ca3af; line-height: 1.5; }
                @media (max-width: 640px) {
                    .features { grid-template-columns: 1fr; }
                    h1 { font-size: 1.75rem; }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="logo">&#9889;</div>
                <h1>Slim Framework + Tabler UI</h1>
                <p class="subtitle">
                    A powerful starter kit combining the lightweight <strong>Slim Framework</strong>
                    with the beautiful <strong>Tabler</strong> admin dashboard UI.
                </p>
                <div class="links">
                    <a href="/dashboard" class="btn-primary">Go to Dashboard</a>
                    <a href="/_setup/download-tabler" class="btn-secondary">Download Tabler Assets</a>
                    <a href="/hello/world" class="btn-secondary">Hello World</a>
                </div>
                <div class="features">
                    <div class="feature">
                        <h3>Slim 4</h3>
                        <p>PSR-7 compliant micro-framework with FastRoute routing, middleware, and DI container support.</p>
                    </div>
                    <div class="feature">
                        <h3>Tabler UI</h3>
                        <p>Premium Bootstrap-based admin dashboard with 500+ components, dark mode, and responsive design.</p>
                    </div>
                    <div class="feature">
                        <h3>Tooling</h3>
                        <p>Clockwork profiling, structured logging, CSRF protection, HTTP caching, and PHP-View templating.</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
    ');
    return $response;
});

$app->get('/hello/{name}', function (Request $request, Response $response, $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    return $response;
});

// ===== Dashboard Routes =====

$app->get('/dashboard', function (Request $request, Response $response) use ($renderer) {
    return $renderer->render($response, '/layouts/dashboard.php', [
        'pageTitle'       => 'Dashboard',
        'pageIcon'        => 'dashboard',
        'pageDescription' => 'Welcome back! Here\'s what\'s happening with your project today.',
        'currentPage'     => 'dashboard',
        'pageTemplate' => '/pages/dashboard.php',
        'data'            => [],
    ]);
});

$app->get('/tables', function (Request $request, Response $response) use ($renderer) {
    return $renderer->render($response, '/layouts/dashboard.php', [
        'pageTitle'       => 'Tables',
        'pageIcon'        => 'table',
        'pageDescription' => 'Manage your data with sortable, searchable tables.',
        'currentPage'     => 'tables',
        'pageTemplate' => '/pages/tables.php',
        'data'            => [],
    ]);
});

$app->get('/blank', function (Request $request, Response $response) use ($renderer) {
    return $renderer->render($response, '/layouts/dashboard.php', [
        'pageTitle'       => 'Blank Page',
        'pageIcon'        => 'file-description',
        'pageDescription' => 'Start building your custom page from this blank template.',
        'currentPage'     => 'blank',
        'pageTemplate' => '/pages/blank.php',
        'data'            => [],
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
