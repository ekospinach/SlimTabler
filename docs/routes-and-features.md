# Routes & Features

This page documents every route defined in `public/routes.php` and the features they expose.

> Legend: 🔓 = public (no auth), 🔒 = requires authentication.

## Route Reference

| Method | Path | Auth | Purpose |
|--------|------|------|---------|
| GET | `/` | 🔓 | Redirects to `/dashboard` if logged in, otherwise to `/login`. |
| GET | `/login` | 🔓 | Displays the login form (`views/pages/login.php`). |
| POST | `/login` | 🔓 | Authenticates via email + password; sets session on success. |
| GET | `/logout` | 🔓 | Destroys the session and redirects to `/login`. |
| GET | `/dashboard` | 🔒 | Main dashboard page (`views/pages/dashboard.php`). |
| GET | `/tables` | 🔒 | Sortable/searchable tables demo (`views/pages/tables.php`). |
| GET | `/blank` | 🔒 | Blank starter page (`views/pages/blank.php`). |
| GET | `/users` | 🔒 | Lists users (`views/pages/users.php`). |
| POST | `/users` | 🔒 | Creates a new user (validated). |
| GET | `/users/{id}/edit` | 🔒 | Edit form for a user (`views/pages/user-form.php`). |
| POST | `/users/{id}/update` | 🔒 | Updates a user (validated). |
| POST | `/users/{id}/delete` | 🔒 | Deletes a user. |
| GET | `/assets/{file:.*}` | 🔒* | Serves static files from `public/assets/` (path-traversal protected). |
| GET | `/_setup/download-tabler` | 🔓 | Downloads Tabler UI assets from the CDN. |

> \* Assets are served even without auth (the `/assets/` prefix is in the auth allow-list), but only for files that resolve inside `public/assets/`.

## Authentication

- **Login** (`POST /login`): looks up the user by email with `getUserByEmail()`, then verifies the password with `password_verify()`. On success it stores `user_id` and `user_name` in the session and redirects to `/dashboard`. On failure it re-renders the login form with an error.
- **Logout** (`GET /logout`): calls `session_destroy()` and redirects to `/login`.
- The **Auth middleware** (`index.php`) allows the paths `/login`, `/logout`, `/hello`, any `/assets/...` path, and any `/_setup/...` path without authentication; everything else is redirected to `/login`.

## Dashboard Pages

The dashboard layout (`views/layouts/dashboard.php`) provides a sidebar and top navbar. Pages are rendered inside it via the `renderDashboard()` helper. Available pages:

- **Dashboard** (`/dashboard`) — overview/welcome page.
- **Tables** (`/tables`) — data table example.
- **Blank Page** (`/blank`) — starter template for new pages.
- **User Management** (`/users`) — user list + create form.

## User Management (CRUD)

The `/users` feature provides full create/read/update/delete of the `users` table.

### Create (`POST /users`)
Validates input:
- `name` is required (non-empty).
- `email` is required and must pass `FILTER_VALIDATE_EMAIL`.
- `password` must be at least **6 characters**.

On validation failure, the form is re-rendered with the aggregated errors via `renderDashboardWithError()`. On a duplicate email, a `PDOException` is caught and the user sees "Email already registered." Success inserts the row with `password_hash($password, PASSWORD_DEFAULT)` and sets a `success` flash message.

### Edit / Update (`GET /users/{id}/edit`, `POST /users/{id}/update`)
The edit form pre-fills `name` and `email` from `getUserById()`. Update re-validates `name` and `email`. If a non-empty `password` is supplied it is re-hashed; otherwise the existing password is preserved.

### Delete (`POST /users/{id}/delete`)
Deletes the row by ID and sets a `success` flash message. No confirmation step is enforced server-side — handle with care in the UI.

## Static Asset Handler — `/assets/{file:.*}`

Serves files from `public/assets/`. Security measures (see [Security](security.md)):
- The requested path is sanitized with `preg_replace('#[^a-zA-Z0-9_./-]#', '', ...)`.
- `realpath()` is used to resolve the final path, and a prefix check ensures it stays within `public/assets`.
- Files served with their detected MIME type and a `Cache-Control: public, max-age=3600` header.

## Asset Downloader — `/_setup/download-tabler`

A setup route (publicly accessible) that downloads Tabler Core `1.4.0` and Tabler Icons `3.31.0` from jsDelivr into `public/assets/tabler/{css,js}`. It attempts `file_get_contents()` and falls back to cURL. The route prints an HTML progress report. Run this once after cloning the project (see [Getting Started](getting-started.md)).

> `public/download-tabler.php` is a deprecated redirect stub to this route.