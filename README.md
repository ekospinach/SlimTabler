# Slim Framework + Tabler UI Dashboard (SlimTabler)

[![Build Status](https://github.com/slimphp/Slim/actions/workflows/tests.yml/badge.svg?branch=4.x)](https://github.com/slimphp/Slim/actions/workflows/tests.yml?query=branch:4.x)
[![Coverage Status](https://coveralls.io/repos/github/slimphp/Slim/badge.svg?branch=4.x)](https://coveralls.io/github/slimphp/Slim?branch=4.x)
[![License](https://poser.pugx.org/slim/slim/license)](https://packagist.org/packages/slim/slim)

**SlimTabler** is a PHP web application built on the [Slim 4 micro-framework](https://www.slimframework.com) (v`4.15.1`) and styled with the [Tabler UI](https://tabler.io) admin dashboard toolkit. It provides a login-protected dashboard with user management, static asset serving, structured request logging, and request profiling.

> This repository is a fork of [`slimphp/Slim`](https://github.com/slimphp/Slim) that layers a Tabler-powered dashboard application on top of the Slim framework source. The `Slim/` directory is the upstream framework; the application lives in `public/` and `views/`.

## Features

- 🔐 **Authentication** — session-based login/logout with `password_hash` / `password_verify`.
- 👥 **User management** — full CRUD over a `users` table with server-side validation.
- 🛡️ **CSRF protection** — session token validation on `POST` requests (returns `403` on mismatch).
- 📊 **Dashboard UI** — Tabler-based layout with sidebar, navbar, and pages (`/dashboard`, `/tables`, `/blank`).
- 🎨 **Tabler assets** — local assets under `public/assets/tabler/` with automatic jsDelivr CDN fallback.
- 📈 **Observability** — structured JSON request logs with correlation IDs (`X-Request-Id`) and [Clockwork](https://underground.works/clockwork) request profiling.
- 🧩 **Custom middleware** — `Slim\Middleware\StructuredLoggingMiddleware`, plus a `Slim\Logger` and `Slim\ResponseEmitter`.

## Requirements

- PHP `7.4`, `8.0` – `8.5`
- Composer
- MySQL / MariaDB (PDO `mysql`)
- PHP extensions: `ext-json`, `ext-pdo`, `ext-simplexml`

## Quick Start

```bash
# 1. Install dependencies
composer install

# 2. Create the database (credentials are in public/bootstrap.php: root / empty password, db "slim")
mysql -e "CREATE DATABASE slim CHARACTER SET utf8mb4;"

# 3. Run the built-in server
php -S localhost:8000 -t public

# 4. Fetch Tabler UI assets (one time, no auth required)
#    Visit: http://localhost:8000/_setup/download-tabler
```

Then open **http://localhost:8000** — you'll be redirected to `/login`. Create your first user via the MySQL CLI (see the [Getting Started](docs/getting-started.md#5-create-your-first-user) guide), then log in.

## Documentation

Full documentation lives in the [`docs/`](docs/README.md) folder:

| Page | Description |
|------|-------------|
| [docs/README.md](docs/README.md) | Documentation home / table of contents. |
| [docs/getting-started.md](docs/getting-started.md) | Installation, database setup, running the app, asset download. |
| [docs/architecture.md](docs/architecture.md) | Request lifecycle, middleware stack, bootstrap helpers, custom Slim classes. |
| [docs/routes-and-features.md](docs/routes-and-features.md) | Full route reference and feature descriptions. |
| [docs/views-and-templating.md](docs/views-and-templating.md) | `slim/php-view` layout system and page variable contract. |
| [docs/security.md](docs/security.md) | CSRF, authentication, path-traversal protection, password hashing, validation. |
| [docs/testing.md](docs/testing.md) | Running the test suite and static analysis tools. |

Additional project docs:

- [OBSERVABILITY.md](OBSERVABILITY.md) — structured logging, correlation IDs, Clockwork profiling.
- [REFACTORING.md](REFACTORING.md) — history of application refactoring sessions.
- [CONTRIBUTING.md](CONTRIBUTING.md) — contribution guidelines.
- [SECURITY.md](SECURITY.md) — how to report security vulnerabilities.
- [UPGRADING.md](UPGRADING.md) — upgrade notes (upstream Slim).

## Running Tests & Quality Tools

```bash
composer test          # phpunit + phpcs + phpstan + psalm
```

See [docs/testing.md](docs/testing.md) for details. Note: the test suite currently covers the `Slim/` framework classes; the dashboard application code is not yet covered by automated tests.

## License

The Slim Framework and this dashboard application are licensed under the MIT license. See [LICENSE.md](LICENSE.md) for more information.