<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$stmt = get_pdo()->prepare('SELECT id, username, password_hash FROM admin_users WHERE id = ?');
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $currentPassword = (string) ($_POST['current_password'] ?? '');
    $newUsername = trim($_POST['username'] ?? '');
    $newPassword = (string) ($_POST['new_password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    if (!password_verify($currentPassword, $admin['password_hash'])) {
        $errors[] = 'Mot de passe actuel incorrect.';
    }
    if (mb_strlen($newUsername) < 3) {
        $errors[] = 'L\'identifiant doit contenir au moins 3 caractères.';
    }
    if ($newPassword !== '' && mb_strlen($newPassword) < 10) {
        $errors[] = 'Le nouveau mot de passe doit contenir au moins 10 caractères.';
    }
    if ($newPassword !== '' && $newPassword !== $confirmPassword) {
        $errors[] = 'Les deux mots de passe ne correspondent pas.';
    }

    if (!$errors) {
        if ($newPassword !== '') {
            $stmt = get_pdo()->prepare('UPDATE admin_users SET username = ?, password_hash = ? WHERE id = ?');
            $stmt->execute([$newUsername, password_hash($newPassword, PASSWORD_DEFAULT), $admin['id']]);
        } else {
            $stmt = get_pdo()->prepare('UPDATE admin_users SET username = ? WHERE id = ?');
            $stmt->execute([$newUsername, $admin['id']]);
        }
        $_SESSION['admin_username'] = $newUsername;
        set_flash('success', 'Compte mis à jour.');
        header('Location: account.php');
        exit;
    }
}

$page_title = 'Mon compte';
$active_nav = 'account';
require_once __DIR__ . '/includes/admin_header.php';
?>

<form class="admin-form" method="post" novalidate>
    <?= csrf_field() ?>

    <?php foreach ($errors as $err): ?>
        <p class="admin-flash admin-flash-error"><?= e($err) ?></p>
    <?php endforeach; ?>

    <div class="form-group">
        <label for="username">Identifiant</label>
        <input class="form-control" type="text" id="username" name="username" required minlength="3" value="<?= e($admin['username']) ?>">
    </div>

    <div class="form-group">
        <label for="new_password">Nouveau mot de passe <span class="hint">(laisser vide pour ne pas changer)</span></label>
        <input class="form-control" type="password" id="new_password" name="new_password" minlength="10" autocomplete="new-password">
    </div>

    <div class="form-group">
        <label for="confirm_password">Confirmer le nouveau mot de passe</label>
        <input class="form-control" type="password" id="confirm_password" name="confirm_password" minlength="10" autocomplete="new-password">
    </div>

    <div class="form-group">
        <label for="current_password">Mot de passe actuel <span class="hint">(requis pour confirmer les changements)</span></label>
        <input class="form-control" type="password" id="current_password" name="current_password" required autocomplete="current-password">
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
