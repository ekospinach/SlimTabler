# Observability and Instrumentation

This document outlines the observability architecture, instrumentation standards, and telemetry setup implemented in the Slim Framework application.

## 1. Questions On-Call Can Answer
With the current telemetry suite, on-call engineers can quickly answer:
1. **What is the overall latency profile of requests?** (via `duration_ms` in structured logs & Clockwork timeline)
2. **Which requests are failing and why?** (via HTTP status codes in `request_completed` logs and PHP Error Middleware)
3. **Can we correlate log events across the entire lifecycle of a request?** (via the unique `requestId` propagated on headers and log events)
4. **Where is time being spent inside a slow transaction?** (via Clockwork timeline trace)

---

## 2. Request Correlation & Structured Logging

All requests are intercepted at the boundary to enforce correlation IDs and produce machine-readable structured JSON logs.

### Middleware Implementation
The logging is managed by `Slim\Middleware\StructuredLoggingMiddleware`. It:
- Extracts `X-Request-Id` from incoming headers or generates a cryptographically secure 128-bit hex ID if missing.
- Attaches the ID as a request attribute (`requestId`) and returns it in the response header `X-Request-Id`.
- Emits structured JSON payloads to system logging.

### Structured Log Schema

#### `request_started`
Fires immediately when the request hits the system boundary.
```json
{
  "event": "request_started",
  "requestId": "2f6a7d8e9b0c1a2f3e4d5c6b7a8f9e0d",
  "method": "GET",
  "uri": "http://localhost:8000/hello/world",
  "timestamp": "2026-07-18T12:00:00+00:00"
}
```

#### `request_completed`
Fires after the response has been generated, tracking status and duration.
```json
{
  "event": "request_completed",
  "requestId": "2f6a7d8e9b0c1a2f3e4d5c6b7a8f9e0d",
  "method": "GET",
  "uri": "http://localhost:8000/hello/world",
  "status": 200,
  "duration_ms": 12.34,
  "timestamp": "2026-07-18T12:00:00+00:00"
}
```

---

## 3. Deep Request Profiling with Clockwork

We use **Clockwork** (`itsgoingd/clockwork`) to provide comprehensive request profiling, timeline tracing, and resource usage metrics.

### Integration
Clockwork is registered as a Slim 4 middleware in `public/index.php`:
```php
$app->add(new \Clockwork\Support\Slim\ClockworkMiddleware($app, __DIR__ . '/../storage/clockwork'));
```

### Metrics & Spans Collected
- **Timeline Spans**: Measures execution times for routing, controller handling, rendering, and custom spans.
- **Resource Profiling**: Captures Peak Memory usage and CPU execution time.
- **Database Telemetry**: Automatically records executed SQL queries, bindings, and durations (if active).
- **HTTP Metadata**: Captures request headers, query parameters, post data, and response payloads safely.

---

## 4. Verification Checklist

Before deploying changes to production, confirm the following telemetry guarantees:
- [x] All log output is structured (JSON) with no prose or string-interpolated messages.
- [x] Every log line contains the stable `requestId` correlation ID.
- [x] Outbound HTTP responses contain the `X-Request-Id` header to assist client-side debugging.
- [x] Profiling metadata directories are writeable by the web server (`storage/clockwork`).
- [x] Log configurations do not leak database passwords, sessions, or unredacted PII.
