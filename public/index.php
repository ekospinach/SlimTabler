<?php
/**
 * Slim Dashboard - Main Entry Point
 * 
 * This file sets up the application, middleware, and includes routes.
 * Route definitions are in routes.php for better organization.
 */

use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/bootstrap.php';

// ========================================================================
// Application Setup
// ========================================================================

$app = AppFactory::create();

// Middleware
$app->addErrorMiddleware(true, true, true);
$app->add(new \Clockwork\Support\Slim\ClockworkMiddleware($app, __DIR__ . '/../storage/clockwork'));
$app->add(new \Slim\Middleware\StructuredLoggingMiddleware());

// CSRF Protection Middleware
$app->add(function ($request, $handler) {
    $method = $request->getMethod();
    
    // Skip CSRF for GET, HEAD, OPTIONS requests
    if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
        return $handler->handle($request);
    }
    
    // Generate CSRF token if not present
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    // For POST requests, get body from stream (not parsed body yet)
    if ($method === 'POST') {
        $body = (string) $request->getBody();
        parse_str($body, $parsedBody);
        $csrfToken = $parsedBody['csrf_token'] ?? $request->getHeaderLine('X-CSRF-Token');
        
        if ($csrfToken !== $_SESSION['csrf_token']) {
            return (new \Slim\Psr7\Response())
                ->withStatus(403)
                ->withHeader('Content-Type', 'text/html')
                ->write('<h1>403 Forbidden</h1><p>Invalid CSRF token.</p>');
        }
    }
    
    return $handler->handle($request);
});

// Auth Middleware
$app->add(function ($request, $handler) {
    $path = $request->getUri()->getPath();
    $public = ['/login', '/logout', '/hello'];
    
    // Allow public routes and asset paths
    if (isLoggedIn() || in_array($path, $public, true) || strpos($path, '/assets/') === 0 || strpos($path, '/_setup/') === 0) {
        return $handler->handle($request);
    }
    
    return redirect('/login');
});

// Renderer Setup
$renderer = new PhpRenderer(__DIR__ . '/../views');
$renderer->setAttributes([
    'basePath' => '',
]);

// ========================================================================
// Load Routes
// ========================================================================

require __DIR__ . '/routes.php';

// ========================================================================
// Run Application
// ========================================================================

$app->run();
