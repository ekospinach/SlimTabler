<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add Clockwork middleware for deep profiling & telemetry
$app->add(new \Clockwork\Support\Slim\ClockworkMiddleware($app, __DIR__ . '/../storage/clockwork'));

// Add Structured Logging middleware for correlation ID & JSON event logging
$app->add(new \Slim\Middleware\StructuredLoggingMiddleware());

// Add routes
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write('<a href="/hello/world">Try /hello/world</a>');
    return $response;
});

$app->get('/hello/{name}', function (Request $request, Response $response, $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    return $response;
});

// ===== Tabler Asset Downloader =====
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

    $log[] = '<hr><p><strong>✅ Download complete!</strong></p>';
    $log[] = '<p>Assets are now in: <code>public/assets/tabler/</code></p>';
    $log[] = '<p><a href="/dashboard">Go to Dashboard</a></p>';

    $response->getBody()->write('<html><head><title>Tabler Download</title><style>body{font-family:sans-serif;max-width:600px;margin:40px auto;line-height:1.6}ul{list-style:none;padding:0}li{padding:4px 0}code{background:#f0f0f0;padding:2px 6px;border-radius:3px}</style></head><body>' . implode("\n", $log) . '</body></html>');
    return $response;
});

$app->run();