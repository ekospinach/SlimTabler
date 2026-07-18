<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Slim Dashboard') ?> — Tabler & Slim</title>

    <!-- Tabler Core CSS (local, fallback to CDN) -->
    <link rel="stylesheet" href="/assets/tabler/css/tabler.min.css" id="tabler-core-css">
    <!-- Tabler Icons (local, fallback to CDN) -->
    <link rel="stylesheet" href="/assets/tabler/css/tabler-icons.min.css" id="tabler-icons-css">
    <script>
    // Robust CSS fallback: if local stylesheets fail to load, fall back to CDN
    (function() {
        var sheets = document.styleSheets;
        var tablerLoaded = false;
        var iconsLoaded = false;
        for (var i = 0; i < sheets.length; i++) {
            var href = sheets[i].href || '';
            if (href.indexOf('tabler.min.css') > -1) tablerLoaded = true;
            if (href.indexOf('tabler-icons.min.css') > -1) iconsLoaded = true;
        }
        if (!tablerLoaded) {
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.min.css';
            document.head.appendChild(link);
        }
        if (!iconsLoaded) {
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.31.0/dist/tabler-icons.min.css';
            document.head.appendChild(link);
        }
    })();
    </script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --tblr-font-sans-serif: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        body {
            font-family: var(--tblr-font-sans-serif);
        }
        .navbar-vertical .navbar-brand-image {
            height: 2rem;
        }
        .page-title-icon {
            margin-right: 0.5rem;
        }
        .stat-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .nav-link-active {
            position: relative;
        }
        .nav-link-active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--tblr-primary);
            border-radius: 0 2px 2px 0;
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
        }
        .sidebar-brand:hover {
            color: var(--tblr-primary);
        }
        .sidebar-brand-icon {
            width: 2rem;
            height: 2rem;
            background: var(--tblr-primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- ===== SIDEBAR ===== -->
        <aside class="navbar navbar-vertical navbar-expand-sm" data-bs-theme="dark">
            <div class="container-fluid">
                <!-- Brand / Logo -->
                <a href="<?= $basePath ?? '' ?>/dashboard" class="sidebar-brand">
                    <div class="sidebar-brand-icon">
                        <i class="ti ti-dashboard"></i>
                    </div>
                    Slim Dashboard
                </a>

                <!-- Sidebar Toggler (mobile) -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Sidebar Navigation -->
                <div class="collapse navbar-collapse" id="sidebar-menu">
                    <ul class="navbar-nav pt-4">
                        <li class="nav-item">
                            <a class="nav-link <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>" href="<?= $basePath ?? '' ?>/dashboard">
                                <i class="ti ti-dashboard nav-link-icon"></i>
                                <span class="nav-link-title">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($currentPage ?? '') === 'tables' ? 'active' : '' ?>" href="<?= $basePath ?? '' ?>/tables">
                                <i class="ti ti-table nav-link-icon"></i>
                                <span class="nav-link-title">Tables</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($currentPage ?? '') === 'blank' ? 'active' : '' ?>" href="<?= $basePath ?? '' ?>/blank">
                                <i class="ti ti-file-description nav-link-icon"></i>
                                <span class="nav-link-title">Blank Page</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <hr class="my-3">
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $basePath ?? '' ?>/">
                                <i class="ti ti-home nav-link-icon"></i>
                                <span class="nav-link-title">Landing</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="https://github.com/tabler/tabler" target="_blank" rel="noopener">
                                <i class="ti ti-brand-github nav-link-icon"></i>
                                <span class="nav-link-title">Tabler on GitHub</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>

        <!-- ===== MAIN CONTENT ===== -->
        <div class="page-wrapper">
            <!-- Top Navbar -->
            <header class="navbar navbar-expand-sm navbar-light d-print-none">
                <div class="container-xl">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <h1 class="navbar-brand navbar-brand-autodark d-sm-none">
                        <a href="<?= $basePath ?? '' ?>/dashboard">Slim Dashboard</a>
                    </h1>
                    <div class="collapse navbar-collapse" id="navbar-menu">
                        <div class="ms-auto d-flex align-items-center gap-3">
                            <!-- Search -->
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <i class="ti ti-search"></i>
                                </span>
                                <input type="text" class="form-control" placeholder="Search…" aria-label="Search">
                            </div>
                            <!-- Theme Toggle -->
                            <a href="#" class="nav-link" id="theme-toggle" title="Toggle theme" data-bs-toggle="theme">
                                <i class="ti ti-sun"></i>
                            </a>
                            <!-- User Avatar -->
                            <div class="dropdown">
                                <a href="#" class="nav-link d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="avatar avatar-sm" style="background-image: url(https://ui-avatars.com/api/?name=Slim+User&background=206bc4&color=fff)"></span>
                                    <span class="d-none d-sm-inline">Slim User</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#">
                                        <i class="ti ti-user me-2"></i> Profile
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <i class="ti ti-settings me-2"></i> Settings
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="#">
                                        <i class="ti ti-logout me-2"></i> Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Header -->
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <h2 class="page-title">
                                <?php if (isset($pageIcon)): ?>
                                    <i class="ti ti-<?= htmlspecialchars($pageIcon) ?> page-title-icon"></i>
                                <?php endif; ?>
                                <?= htmlspecialchars($pageTitle ?? 'Dashboard') ?>
                            </h2>
                            <?php if (isset($pageDescription)): ?>
                                <div class="text-muted mt-1"><?= htmlspecialchars($pageDescription) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-auto ms-auto d-print-none">
                            <div class="btn-list">
                                <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-report">
                                    <i class="ti ti-plus me-1"></i>
                                    New Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Body -->
            <div class="page-body">
                <div class="container-xl">
                    <?= $this->fetch($template, $data) ?>
                </div>
            </div>

            <!-- Footer -->
            <footer class="footer footer-transparent d-print-none">
                <div class="container-xl">
                    <div class="row text-center align-items-center flex-row-reverse">
                        <div class="col-lg-auto ms-lg-auto">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item">
                                    <a href="https://www.slimframework.com" target="_blank" rel="noopener" class="link-secondary">Slim Framework</a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="https://github.com/tabler/tabler" target="_blank" rel="noopener" class="link-secondary">Tabler UI Kit</a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                            <p class="text-secondary mb-0">
                                Built with <a href="https://www.slimframework.com" target="_blank" rel="noopener" class="link-secondary">Slim Framework <?= \Slim\App::VERSION ?></a> &amp;
                                <a href="https://tabler.io" target="_blank" rel="noopener" class="link-secondary">Tabler</a> &mdash;
                                &copy; <?= date('Y') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Modal: New Report -->
    <div class="modal modal-blur fade" id="modal-report" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Report Title</label>
                        <input type="text" class="form-control" placeholder="Enter report title">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" rows="3" placeholder="Report description…"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select">
                                    <option value="1">Sales</option>
                                    <option value="2">Marketing</option>
                                    <option value="3">Development</option>
                                    <option value="4">Support</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Priority</label>
                                <select class="form-select">
                                    <option value="1">Low</option>
                                    <option value="2">Medium</option>
                                    <option value="3">High</option>
                                    <option value="4">Critical</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary ms-auto" data-bs-dismiss="modal">
                        <i class="ti ti-send me-1"></i>
                        Create Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabler Core JS (local, fallback to CDN) -->
    <script src="/assets/tabler/js/tabler.min.js" onerror="this.onerror=null;this.src='https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler.min.js'"></script>
    <script>
    // Ensure Tabler JS is loaded; if local failed, it will already have fallen back via onerror.
    // This also re-triggers any Tabler initialization after CDN fallback loads.
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.tabler === 'undefined' && typeof $ === 'undefined') {
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler.min.js';
            script.onload = function() {
                // Re-initialize Tabler components if needed
                if (window.tabler && window.tabler.core) {
                    window.tabler.core.init();
                }
            };
            document.body.appendChild(script);
        }
    });
    </script>
</body>
</html>
