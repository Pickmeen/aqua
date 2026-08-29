<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: events.php');
    exit;
}

verify_csrf();

$id = (int) ($_POST['id'] ?? 0);
if ($id) {
    $stmt = get_pdo()->prepare('DELETE FROM events WHERE id = ?');
    $stmt->execute([$id]);
    set_flash('success', 'Événement supprimé.');
}

header('Location: events.php');
exit;
