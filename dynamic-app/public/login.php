<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

if (current_user()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (login($username, $password)) {
        header('Location: index.php');
        exit;
    }

    $error = 'Username atau password salah.';
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="login-page">
    <main class="login-card">
        <p class="eyebrow">UAS Cloud Computing II</p>
        <h1><?= e(APP_NAME) ?></h1>
        <p class="muted">Masuk untuk mengelola data CRUD yang sangat sederhana.</p>

        <?php if ($error !== ''): ?>
            <div class="alert error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <label>
                Username
                <input name="username" autocomplete="username" required autofocus>
            </label>
            <label>
                Password
                <input type="password" name="password" autocomplete="current-password" required>
            </label>
            <button type="submit">Login</button>
        </form>

        <p class="hint">Demo: admin / admin123</p>
    </main>
</body>
</html>
