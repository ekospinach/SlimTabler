# Architecture

This document explains how the **SlimTabler** application is wired together — from the entry point, through the middleware stack, to rendering.

## High-Level Overview

```
Browser
  │
  ▼
public/index.php        ← bootstrap, middleware registration, renderer setup, route include
  │
  ├─ bootstrap.php      ← session, DB singleton, auth/flash/redirect helpers
  │
  ▼
Middleware Stack (outer → inner)
  ├─ ErrorMiddleware              (Slim built-in)
  ├─ ClockworkMiddleware          (request profiling)
  ├─ StructuredLoggingMiddleware  (correlation IDs + JSON logs)
  ├─ CSRF Middleware (closure)    (token check on POST)
  └─ Auth Middleware (closure)    (redirect unauthenticated users)
  │
  ▼
public/routes.php      ← route definitions (Slim\App + PhpRenderer)
  │
  ▼
PhpRenderer → views/layouts/dashboard.php → $this->fetch(pageTemplate, pageData)
  │
  ▼
Slim\ResponseEmitter   ← sends headers + body to the client
```

## Entry Point — `public/index.php`

The entry point is intentionally minimal. It performs four jobs:

1. `require`s the Composer autoloader and `bootstrap.php`.
2. Creates the app via `AppFactory::create()` and registers middleware.
3. Configures a `PhpRenderer` rooted at `views/` with a `basePath` attribute.
4. `require`s `routes.php` and calls `$app->run()`.

## Middleware Stack

Middleware are added with `$app->add(...)` and execute **outermost-first**. The order registered in `index.php`:

| # | Middleware | Source | Responsibility |
|---|-----------|--------|----------------|
| 1 | Error Middleware | `Slim\Middleware\ErrorMiddleware` | Catches exceptions, renders error pages, logs. `addErrorMiddleware(true, true, true)`. |
| 2 | Clockwork | `\Clockwork\Support\Slim\ClockworkMiddleware` | Profiles requests; writes timelines to `storage/clockwork`. |
| 3 | Structured Logging | `Slim\Middleware\StructuredLoggingMiddleware` | Generates/propagates `X-Request-Id`, emits `request_started` / `request_completed` JSON logs. |
| 4 | CSRF (closure) | `public/index.php` | Generates a session CSRF token; validates it on POST; returns `403` on mismatch. |
| 5 | Auth (closure) | `public/index.php` | Allows public routes; redirects others to `/login` when not authenticated. |

> The CSRF and Auth middlewares are defined as inline closures in `index.php` (not separate classes).

## Bootstrap Helpers — `public/bootstrap.php`

Shared, globally-scoped helper functions used throughout the routes.

### Database
- `db(): PDO` — singleton PDO connection to the `slim` MySQL database.
- `ensureUsersTable(): void` — idempotently creates the `users` table.
- `getUsers(): array` — all users ordered by `created_at DESC`.
- `getUserById(int $id): ?array` — lookup by primary key.
- `getUserByEmail(string $email): ?array` — lookup by email (used for login).

### Authentication
- `isLoggedIn(): bool` — true when `$_SESSION['user_id']` is set.
- `getCurrentUserId(): ?int` — current user's ID.
- `getCurrentUserName(): string` — current user's display name (defaults to `Guest`).
- `requireAuth(): void` — `header('Location: /login')` + `exit` if not logged in.

### Responses & Flash
- `redirect(string $url, int $status = 302): ResponseInterface` — builds a `Slim\Psr7\Response` with a `Location` header.
- `setFlash(string $type, string $message): void` — stores a flash message in `$_SESSION['flash']`.
- `getFlash(string $type): ?string` — retrieves **and clears** a flash message.
- `hasFlash(string $type): bool` — checks for a pending flash message.

## Custom Slim Framework Classes

This fork extends the base Slim 4 framework with a few custom classes under the `Slim\` namespace.

### `Slim\Middleware\StructuredLoggingMiddleware`
Implements `Psr\Http\Server\MiddlewareInterface`. On each request it:
- Reads `X-Request-Id` from the incoming headers, or generates a secure 128-bit hex ID (`bin2hex(random_bytes(16))`).
- Attaches the ID to the request as the `requestId` attribute and echoes it back in the response `X-Request-Id` header.
- Logs `request_started` and `request_completed` events as JSON via `error_log()`.

See [OBSERVABILITY.md](../OBSERVABILITY.md) for the full log schema.

### `Slim\Logger`
A `Psr\Log\AbstractLogger` implementation whose `log()` method forwards the message to PHP's `error_log()`. Used as the default logger for error middleware when none is supplied.

### `Slim\ResponseEmitter`
Responsible for sending the PSR-7 `Response` to the client: emits headers, the status line (after headers, to avoid `Location` header quirks), and the body in chunked writes. Treats status codes `204`, `205`, and `304` (and empty bodies) as having no body.

## Rendering

Rendering uses **`slim/php-view`** (`Slim\Views\PhpRenderer`). Pages are rendered inside `views/layouts/dashboard.php`, which injects the requested page via `$this->fetch($pageTemplate, $pageData)`. The two `renderDashboard()` / `renderDashboardWithError()` helpers in `routes.php` standardize this contract. See [Views & Templating](views-and-templating.md).

## Observability

Request profiling and structured logging are documented in detail in [OBSERVABILITY.md](../OBSERVABILITY.md).