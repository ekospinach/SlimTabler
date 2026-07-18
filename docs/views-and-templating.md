# Views & Templating

The application renders HTML using **`slim/php-view`** (`Slim\Views\PhpRenderer`). Templates are plain PHP files with access to variables passed by the route handlers.

## Renderer Setup

In `public/index.php`:

```php
$renderer = new PhpRenderer(__DIR__ . '/../views');
$renderer->setAttributes([
    'basePath' => '',
]);
```

The renderer root is the `views/` directory. `basePath` is available to every template (useful if the app is mounted under a sub-path).

## Layout System

All authenticated pages are rendered inside a shared layout, `views/layouts/dashboard.php`. The layout:
- Renders the `<head>` with Tabler CSS, Google Fonts, and custom styles.
- Renders the sidebar navigation and top navbar.
- Injects the page body via `$this->fetch($pageTemplate, $pageData)` inside `.page-body`.
- Renders the footer and loads Tabler JS.

## Page Variable Contract

Route handlers pass a consistent set of variables to the layout. The `renderDashboard()` and `renderDashboardWithError()` helpers in `routes.php` build this contract for you.

| Variable | Type | Description |
|----------|------|-------------|
| `pageTitle` | string | Page heading shown in the header and `<title>`. |
| `pageIcon` | string | Tabler icon name (without the `ti-` prefix), e.g. `users`. |
| `pageDescription` | string | Subtitle shown under the page title. |
| `currentPage` | string | Active-nav key (`dashboard`, `tables`, `blank`, `users`) for highlighting. |
| `pageTemplate` | string | Path to the page template, relative to `views/` (e.g. `/pages/users.php`). |
| `pageData` | array | Variables passed through to the page template. |
| `formError` | string | (only via `renderDashboardWithError`) Error message rendered in the page. |

### `renderDashboard()` signature
```php
function renderDashboard(
    Response $response,
    PhpRenderer $renderer,
    string $pageTitle,
    string $pageIcon,
    string $pageDescription,
    string $currentPage,
    string $pageTemplate,
    array $pageData = []
): Response;
```

### `renderDashboardWithError()` signature
Same as above, plus `string $errorMessage` and an optional `array $extraData = []` merged into `pageData` (with `formError` set to the message).

## Templates

| Template | Purpose |
|----------|---------|
| `views/layouts/dashboard.php` | Shared authenticated layout. |
| `views/pages/login.php` | Standalone login page (no dashboard layout). |
| `views/pages/dashboard.php` | Dashboard overview. |
| `views/pages/tables.php` | Data tables example. |
| `views/pages/blank.php` | Blank starter page. |
| `views/pages/users.php` | User list + create form. |
| `views/pages/user-form.php` | Edit-user form (reused for editing). |

## Tabler Asset Wiring & CDN Fallback

The dashboard references local Tabler assets under `/assets/tabler/`:

```html
<link rel="stylesheet" href="/assets/tabler/css/tabler.min.css" id="tabler-core-css">
<link rel="stylesheet" href="/assets/tabler/css/tabler-icons.min.css" id="tabler-icons-css">
<script src="/assets/tabler/js/tabler.min.js" onerror="...CDN fallback..."></script>
```

If the local assets are missing, the templates **fall back to the jsDelivr CDN** automatically:
- CSS: a small inline script checks `document.styleSheets` and appends CDN `<link>` tags if `tabler.min.css` / `tabler-icons.min.css` did not load.
- JS: the `<script>` `onerror` handler swaps in the CDN URL, and a `DOMContentLoaded` listener re-initializes Tabler if needed.

This means the app still renders correctly even before you run `/_setup/download-tabler` (at the cost of external requests).

## Adding a New Page

1. Create `views/pages/my-page.php` using `$pageData` variables.
2. Add a route in `public/routes.php` that calls `renderDashboard($response, $renderer, 'My Page', 'icon', 'Desc', 'mypage', '/pages/my-page.php')`.
3. (Optional) Add a sidebar link in `views/layouts/dashboard.php` keyed to your `currentPage` value.