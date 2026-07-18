# Testing & Quality

This document covers how to run the project's test suite and static analysis tooling, and the current coverage of the application code.

## Running Everything

From the project root:

```bash
composer test
```

This single command runs, in order (defined in `composer.json` scripts):

1. `phpunit` — unit tests
2. `phpcs` — coding standard checks
3. `phpstan --memory-limit=-1` — static analysis (level from `phpstan.neon.dist`)
4. `psalm --no-cache` — static analysis

## PHPUnit

Configuration lives in `phpunit.xml.dist`:

- **Bootstrap:** `tests/bootstrap.php`
- **Test suite:** the `tests/` directory (excluding `tests/Mocks`)
- **Strictness:** `beStrictAboutChangesToGlobalState` and `beStrictAboutOutputDuringTests` are enabled.
- **Execution order:** `random` (helps catch order-dependent tests).
- **Coverage:** HTML report to `coverage/`; source included from the `Slim/` directory.

Run a subset or with coverage:

```bash
# All tests
vendor/bin/phpunit

# With coverage (requires Xdebug or PCOV)
vendor/bin/phpunit --coverage-html coverage

# A single test file
vendor/bin/phpunit tests/ResponseEmitterTest.php
```

### What the tests cover

The `tests/` directory contains the **upstream Slim framework test suite** and validates the framework classes under `Slim/`, for example:

- `tests/AppTest.php`, `tests/Factory/AppFactoryTest.php`
- `tests/MiddlewareDispatcherTest.php`, `tests/ResponseEmitterTest.php`
- `tests/Middleware/*` (BodyParsing, Error, Routing, OutputBuffering, etc.)
- `tests/Routing/*` (Route, RouteCollector, Dispatcher, RouteRunner, …)
- `tests/Handlers/*`, `tests/Error/*`, `tests/Exception/*`

### What is NOT covered

> ⚠️ The **dashboard application code** — `public/index.php`, `public/bootstrap.php`, `public/routes.php`, and the `views/` templates — is **not covered by automated tests**. There are no tests for the route handlers, authentication flow, CSRF middleware, or user CRUD. This is noted as a future improvement in `REFACTORING.md` (testing recommendations: verify routes, CSRF, auth flow, flash messages, and user CRUD).

## Code Quality Tools

| Tool | Config File | Purpose |
|------|-------------|---------|
| PHP_CodeSniffer | `phpcs.xml.dist` | Enforces the project's coding standard. Run with `vendor/bin/phpcs`. |
| PHPStan | `phpstan.neon.dist` | Static analysis for type and logic errors. Run with `composer phpstan`. |
| Psalm | `psalm.xml` | Additional static analysis. Run with `composer psalm`. |

## Coverage Reporting

`phpunit.xml.dist` is configured to emit an HTML coverage report to `coverage/`. The project is also wired for [Coveralls](https://coveralls.io) via `.coveralls.yml` for CI coverage tracking.