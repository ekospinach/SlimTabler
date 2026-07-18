# SlimTabler Documentation

Welcome to the documentation for **SlimTabler** — a [Slim 4](https://www.slimframework.com) PHP micro-framework application packaged with the [Tabler UI](https://tabler.io) admin dashboard toolkit.

This documentation describes the application's actual functionality: a login-protected dashboard with user management, static asset serving, and an asset-download setup route.

## Table of Contents

| Page | Description |
|------|-------------|
| [Getting Started](getting-started.md) | Requirements, installation, database setup, running the app, and fetching UI assets. |
| [Architecture](architecture.md) | Request lifecycle, middleware stack, bootstrap helpers, and custom Slim classes. |
| [Routes & Features](routes-and-features.md) | Full route reference and feature descriptions (auth, dashboard, user CRUD). |
| [Views & Templating](views-and-templating.md) | The `slim/php-view` layout system, page variable contract, and Tabler asset wiring. |
| [Security](security.md) | CSRF, authentication, path-traversal protection, password hashing, and input validation. |
| [Testing & Quality](testing.md) | Running the test suite and static analysis tools. |

## Additional Project Docs

- [OBSERVABILITY.md](../OBSERVABILITY.md) — structured logging, request correlation (`X-Request-Id`), and Clockwork profiling.
- [REFACTORING.md](../REFACTORING.md) — history of the application's refactoring sessions.
- [CONTRIBUTING.md](../CONTRIBUTING.md) — contribution guidelines.
- [SECURITY.md](../SECURITY.md) — how to report security vulnerabilities.

## Project Version

This application is built on **Slim Framework `4.15.1`** (see `Slim\App::VERSION`).