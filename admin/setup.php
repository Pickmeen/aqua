<?php
/**
 * Création du tout premier compte administrateur.
 * Se désactive automatiquement dès qu'un compte existe : ce fichier peut
 * rester sur le serveur sans risque, mais il est recommandé de le
 * supprimer une fois le compte créé (voir README.md).
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/icons.php';

$hasAdmin = (int) get_pdo()->query('SELECT COUNT(*) AS c FROM admin_users')->fetch()['c'] > 0;
if ($hasAdmin) {
    header('Location: login.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $username = trim($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['confirm'] ?? '');

    if (mb_strlen($username) < 3) {
        $errors[] = 'L\'identifiant doit contenir au moins 3 caractères.';
    }
    if (mb_strlen($password) < 10) {
        $errors[] = 'Le mot de passe doit contenir au moins 10 caractères.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Les deux mots de passe ne correspondent pas.';
    }

    if (!$errors) {
        $stmt = get_pdo()->prepare('INSERT INTO admin_users (username, password_hash) VALUES (?, ?)');
        $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
        set_flash('success', 'Compte administrateur créé. Tu peux te connecter — puis supprime admin/setup.php par sécurité.');
        header('Location: login.php');
        exit;
    }
}
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Créer le compte administrateur — Plongée Carpentras</title>
<meta name="robots" content="noindex, nofollow">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="login-page">
    <div class="login-card">
        <?php render_icon('anchor', 'icon icon-xl'); ?>
        <h1>Premier compte admin</h1>
        <p class="lead">Cette page ne fonctionne qu'une seule fois.</p>

        <?php foreach ($errors as $err): ?>
            <p class="admin-flash admin-flash-error"><?= e($err) ?></p>
        <?php endforeach; ?>

        <form method="post" novalidate>
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="username">Identifiant</label>
                <input class="form-control" type="text" id="username" name="username" required minlength="3" autofocus>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input class="form-control" type="password" id="password" name="password" required minlength="10">
                <span class="hint">10 caractères minimum.</span>
            </div>
            <div class="form-group">
                <label for="confirm">Confirmer le mot de passe</label>
                <input class="form-control" type="password" id="confirm" name="confirm" required minlength="10">
            </div>
            <button type="submit" class="btn btn-primary">Créer le compte</button>
        </form>
    </div>
</div>
</body>
</html>
