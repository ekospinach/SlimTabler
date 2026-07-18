<?php
// Login page (standalone, no dashboard layout)
$error = $error ?? '';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Slim Dashboard</title>
    <link rel="stylesheet" href="/assets/tabler/css/tabler.min.css">
    <link rel="stylesheet" href="/assets/tabler/css/tabler-icons.min.css">
    <style>
        body { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { width: 100%; max-width: 420px; }
    </style>
</head>
<body class="border-top-wide border-top-primary d-flex flex-column">
    <div class="container-tight py-4">
        <div class="card card-md">
            <div class="card-body">
                <h2 class="h2 text-center mb-4">Slim Dashboard</h2>
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post" action="/login" autocomplete="off">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="you@example.com" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Your password" required>
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">Sign in</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="/assets/tabler/js/tabler.min.js"></script>
</body>
</html>
