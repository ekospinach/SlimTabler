# Getting Started

This guide walks you through installing and running the **SlimTabler** dashboard application on your local machine.

## Requirements

| Component | Version |
|-----------|---------|
| PHP | `7.4`, `8.0` – `8.5` (as declared in `composer.json`) |
| Composer | Latest stable |
| MySQL / MariaDB | Any recent version (PDO `mysql` extension required) |
| PHP Extensions | `ext-json`, `ext-pdo`, `ext-simplexml` (for XML body parsing) |

## 1. Install Dependencies

Clone the repository and install dependencies with Composer:

```bash
git clone https://github.com/ekospinach/SlimTabler.git
cd SlimTabler
composer install
```

## 2. Configure the Database

The application connects to a MySQL database named **`slim`** using a PDO singleton defined in `public/bootstrap.php`:

```php
$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=slim;charset=utf8mb4';
$pdo = new PDO($dsn, 'root', '', [ /* ... */ ]);
```

> ⚠️ **Development only:** The credentials (`root` / empty password) are hardcoded in `public/bootstrap.php`. Update them before any production deployment.

Create the database and a matching user:

```sql
CREATE DATABASE slim CHARACTER SET utf8mb4;
-- (optional) CREATE USER ...; GRANT ALL PRIVILEGES ON slim.* TO ...;
```

The `users` table is created automatically on first access via `ensureUsersTable()` — no manual migration is required. Schema:

```sql
CREATE TABLE users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120) NOT NULL,
    email       VARCHAR(191) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## 3. Run the Application

Use PHP's built-in development server with `public/` as the document root:

```bash
php -S localhost:8000 -t public
```

Then open **http://localhost:8000** in your browser. You will be redirected to `/login`.

## 4. Fetch the Tabler UI Assets

Static UI assets (CSS/JS/fonts) are served from `public/assets/tabler/`. To download them from the CDN, visit the setup route once (no authentication required):

```
http://localhost:8000/_setup/download-tabler
```

This downloads Tabler Core `1.4.0` and Tabler Icons `3.31.0` into `public/assets/tabler/{css,js}`.

> 💡 If assets are missing at runtime, the layout templates automatically fall back to the jsDelivr CDN (see [Views & Templating](views-and-templating.md)). You can also run the helper script `public/download-assets.bat` on Windows.

> 🗑️ `public/download-tabler.php` is a deprecated redirect stub that forwards to `/_setup/download-tabler`. It can be deleted.

## 5. Create Your First User

There is no seeded admin account. To log in for the first time, insert a user directly, or temporarily allow self-registration. The simplest approach is to insert a user via the MySQL CLI:

```sql
INSERT INTO users (name, email, password)
VALUES ('Admin', 'admin@example.com', PASSWORD_PLACEHOLDER);
```

Then hash the password properly using PHP before inserting:

```bash
php -r "echo password_hash('your-password', PASSWORD_DEFAULT);"
```

Copy the resulting hash into the `password` column. You can then log in at `/login` and manage users from the **User Management** page at `/users`.

## Next Steps

- Read the [Architecture](architecture.md) guide to understand the request lifecycle.
- See [Routes & Features](routes-and-features.md) for the full route reference.
- Review [Security](security.md) for CSRF, auth, and validation details.