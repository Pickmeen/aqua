<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);
$event = ['title' => '', 'event_date' => '', 'event_time' => '', 'description' => ''];
$errors = [];

if ($id) {
    $stmt = get_pdo()->prepare('SELECT * FROM events WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        set_flash('error', 'Événement introuvable.');
        header('Location: events.php');
        exit;
    }
    $event = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $event['title'] = trim($_POST['title'] ?? '');
    $event['event_date'] = trim($_POST['event_date'] ?? '');
    $event['event_time'] = trim($_POST['event_time'] ?? '') ?: null;
    $event['description'] = trim($_POST['description'] ?? '') ?: null;

    if ($event['title'] === '') {
        $errors[] = 'Le titre est obligatoire.';
    }
    if (!DateTime::createFromFormat('Y-m-d', $event['event_date'])) {
        $errors[] = 'La date est invalide.';
    }

    if (!$errors) {
        if ($id) {
            $stmt = get_pdo()->prepare('UPDATE events SET title=?, event_date=?, event_time=?, description=? WHERE id=?');
            $stmt->execute([$event['title'], $event['event_date'], $event['event_time'], $event['description'], $id]);
            set_flash('success', 'Événement mis à jour.');
        } else {
            $stmt = get_pdo()->prepare('INSERT INTO events (title, event_date, event_time, description) VALUES (?, ?, ?, ?)');
            $stmt->execute([$event['title'], $event['event_date'], $event['event_time'], $event['description']]);
            set_flash('success', 'Événement ajouté.');
        }
        header('Location: events.php');
        exit;
    }
}

$page_title = 'Événement';
$active_nav = 'events';
require_once __DIR__ . '/includes/admin_header.php';
?>

<a href="events.php" style="color:var(--depth-mid); font-weight:700; font-size:.88rem; display:inline-block; margin-bottom:1.2rem;">&larr; Retour au calendrier</a>

<form class="admin-form" method="post" novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= (int) $id ?>">

    <?php foreach ($errors as $err): ?>
        <p class="admin-flash admin-flash-error"><?= e($err) ?></p>
    <?php endforeach; ?>

    <div class="form-group">
        <label for="title">Titre</label>
        <input class="form-control" type="text" id="title" name="title" required value="<?= e($event['title']) ?>">
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="event_date">Date</label>
            <input class="form-control" type="date" id="event_date" name="event_date" required value="<?= e($event['event_date']) ?>">
        </div>
        <div class="form-group">
            <label for="event_time">Heure <span class="hint">(optionnel)</span></label>
            <input class="form-control" type="time" id="event_time" name="event_time" value="<?= e($event['event_time'] ? substr($event['event_time'], 0, 5) : '') ?>">
        </div>
    </div>

    <div class="form-group">
        <label for="description">Description <span class="hint">(optionnel)</span></label>
        <textarea class="form-control" id="description" name="description"><?= e($event['description']) ?></textarea>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= $id ? 'Enregistrer' : 'Ajouter' ?></button>
        <a href="events.php" class="btn btn-outline">Annuler</a>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
