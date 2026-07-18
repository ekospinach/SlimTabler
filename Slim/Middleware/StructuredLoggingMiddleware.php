<?php

declare(strict_types=1);

namespace Slim\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class StructuredLoggingMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $startTime = microtime(true);

        // Get or generate request ID (correlation ID)
        $requestId = $request->getHeaderLine('X-Request-Id');
        if (empty($requestId)) {
            $requestId = bin2hex(random_bytes(16));
        }

        // Attach request ID to request attributes
        $request = $request->withAttribute('requestId', $requestId);

        // Log request started (Structured JSON log)
        $startLog = [
            'event' => 'request_started',
            'requestId' => $requestId,
            'method' => $request->getMethod(),
            'uri' => (string) $request->getUri(),
            'timestamp' => date('c'),
        ];
        error_log(json_encode($startLog));

        // Process request
        $response = $handler->handle($request);

        // Calculate duration
        $durationMs = round((microtime(true) - $startTime) * 1000, 2);

        // Set request ID on the response header
        $response = $response->withHeader('X-Request-Id', $requestId);

        // Log request completed (Structured JSON log)
        $completedLog = [
            'event' => 'request_completed',
            'requestId' => $requestId,
            'method' => $request->getMethod(),
            'uri' => (string) $request->getUri(),
            'status' => $response->getStatusCode(),
            'duration_ms' => $durationMs,
            'timestamp' => date('c'),
        ];
        error_log(json_encode($completedLog));

        return $response;
    }
}
