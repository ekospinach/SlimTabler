# Security

This document covers the security controls implemented in the **SlimTabler** application.

## CSRF Protection

A CSRF middleware (inline closure in `public/index.php`) protects state-changing requests:

- **Token generation:** if `$_SESSION['csrf_token']` is empty, a 256-bit token is generated with `bin2hex(random_bytes(32))` and stored in the session.
- **Safe methods:** `GET`, `HEAD`, and `OPTIONS` requests pass through without validation.
- **POST validation:** for `POST` requests, the token is read from the parsed body (`csrf_token`) or the `X-CSRF-Token` header. If it does not match the session token, the middleware returns a `403 Forbidden` response.
- **Body handling:** the raw body is read via `(string) $request->getBody()` and parsed with `parse_str()` *before* Slim parses it, so the token is captured even when the body has already been consumed.

> ⚠️ The middleware validates the token but **does not automatically inject** the token into forms. Templates must include a hidden `<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">` field (or the `X-CSRF-Token` header) on every POST form. This is listed as a future improvement in `REFACTORING.md`.

## Authentication

The **Auth middleware** (inline closure in `public/index.php`) enforces access control:

- Public paths that bypass authentication: `/login`, `/logout`, `/hello`, any path starting with `/assets/`, and any path starting with `/_setup/`.
- All other paths require an authenticated session (`isLoggedIn()`). Unauthenticated requests are redirected to `/login`.
- Authentication state is stored in `$_SESSION['user_id']` / `$_SESSION['user_name']`, set by `POST /login` after a successful `password_verify()`.

> ⚠️ The `/hello` path is in the public allow-list but is **not defined as a route** in `routes.php`. It is harmless (Slim returns 404) but should be reviewed.

## Static Asset Path-Traversal Protection

The `/assets/{file:.*}` handler serves files from `public/assets/`. It protects against directory traversal:

1. The requested file is sanitized, stripping any character outside `[a-zA-Z0-9_./-]`:
   ```php
   $rel = preg_replace('#[^a-zA-Z0-9_./-]#', '', $args['file']);
   ```
2. The resolved path is confirmed with `realpath()`, and a prefix check ensures it starts with the real `public/assets` directory:
   ```php
   if ($realPath === false || $assetsDir === false || strpos($realPath, $assetsDir) !== 0) {
       return $response->withStatus(404);
   }
   ```
3. Non-file targets return `404`.

## Password Storage

User passwords are never stored in plaintext:
- **Create / Update:** `password_hash($password, PASSWORD_DEFAULT)` (currently bcrypt).
- **Login:** `password_verify($password, $user['password'])`.

## Input Validation

User input is validated server-side in the `/users` handlers (`public/routes.php`):
- `name` must be non-empty.
- `email` must be non-empty and pass `FILTER_VALIDATE_EMAIL`.
- `password` (on create) must be at least **6 characters**.
- Duplicate emails are caught via a `PDOException` on the unique constraint and surfaced as "Email already registered."

Output is escaped in templates with `htmlspecialchars()` (see `views/layouts/dashboard.php`, `views/pages/login.php`).

## Reporting Vulnerabilities

Please report security issues privately per [SECURITY.md](../SECURITY.md) rather than opening a public issue.