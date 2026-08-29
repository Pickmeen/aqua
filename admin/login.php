<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/icons.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    if (login_attempts_exceeded()) {
        $error = 'Trop de tentatives. Merci de réessayer dans une minute.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        $stmt = get_pdo()->prepare('SELECT id, username, password_hash FROM admin_users WHERE username = ?');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            reset_login_attempts();
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: index.php');
            exit;
        }

        register_failed_login();
        $error = 'Identifiants incorrects.';
    }
}

// Aucun compte admin créé : on oriente vers la création initiale.
$hasAdmin = (int) get_pdo()->query('SELECT COUNT(*) AS c FROM admin_users')->fetch()['c'] > 0;
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion administration — Plongée Carpentras</title>
<meta name="robots" content="noindex, nofollow">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="login-page">
    <div class="login-card">
        <?php render_icon('bubbles', 'icon icon-xl'); ?>
        <h1>Espace administration</h1>
        <p class="lead">Plongée Carpentras — CSCV</p>

        <?php if (!$hasAdmin): ?>
            <p class="admin-flash admin-flash-error" style="text-align:left;">
                Aucun compte administrateur n'existe encore. <a href="setup.php">Crée le premier compte ici</a>.
            </p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p class="admin-flash admin-flash-error"><?= e($error) ?></p>
        <?php endif; ?>

        <form method="post" novalidate>
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="username">Identifiant</label>
                <input class="form-control" type="text" id="username" name="username" autocomplete="username" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input class="form-control" type="password" id="password" name="password" autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn btn-primary">Se connecter</button>
        </form>
    </div>
</div>
</body>
</html>
