<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/icons.php';

$available_icons = ['bubbles', 'child', 'medal', 'first-dive', 'whistle', 'fin', 'compass', 'depth-gauge', 'anchor'];

$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);
$formation = ['title' => '', 'summary' => '', 'details' => '', 'icon' => 'bubbles', 'depth_label' => 'Piscine', 'sort_order' => 0];
$errors = [];

if ($id) {
    $stmt = get_pdo()->prepare('SELECT * FROM formations WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        set_flash('error', 'Formation introuvable.');
        header('Location: formations.php');
        exit;
    }
    $formation = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $formation['title'] = trim($_POST['title'] ?? '');
    $formation['summary'] = trim($_POST['summary'] ?? '');
    $formation['details'] = trim($_POST['details'] ?? '') ?: null;
    $formation['icon'] = in_array($_POST['icon'] ?? '', $available_icons, true) ? $_POST['icon'] : 'bubbles';
    $formation['depth_label'] = trim($_POST['depth_label'] ?? '') ?: 'Piscine';
    $formation['sort_order'] = (int) ($_POST['sort_order'] ?? 0);

    if ($formation['title'] === '') {
        $errors[] = 'Le titre est obligatoire.';
    }
    if ($formation['summary'] === '') {
        $errors[] = 'Le résumé est obligatoire.';
    }

    if (!$errors) {
        if ($id) {
            $stmt = get_pdo()->prepare('UPDATE formations SET title=?, summary=?, details=?, icon=?, depth_label=?, sort_order=? WHERE id=?');
            $stmt->execute([$formation['title'], $formation['summary'], $formation['details'], $formation['icon'], $formation['depth_label'], $formation['sort_order'], $id]);
            set_flash('success', 'Formation mise à jour.');
        } else {
            $stmt = get_pdo()->prepare('INSERT INTO formations (title, summary, details, icon, depth_label, sort_order) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$formation['title'], $formation['summary'], $formation['details'], $formation['icon'], $formation['depth_label'], $formation['sort_order']]);
            set_flash('success', 'Formation ajoutée.');
        }
        header('Location: formations.php');
        exit;
    }
}

$page_title = 'Formation';
$active_nav = 'formations';
require_once __DIR__ . '/includes/admin_header.php';
?>

<a href="formations.php" style="color:var(--depth-mid); font-weight:700; font-size:.88rem; display:inline-block; margin-bottom:1.2rem;">&larr; Retour aux formations</a>

<form class="admin-form" method="post" novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= (int) $id ?>">

    <?php foreach ($errors as $err): ?>
        <p class="admin-flash admin-flash-error"><?= e($err) ?></p>
    <?php endforeach; ?>

    <div class="form-group">
        <label for="title">Titre</label>
        <input class="form-control" type="text" id="title" name="title" required value="<?= e($formation['title']) ?>">
    </div>

    <div class="form-group">
        <label for="summary">Résumé <span class="hint">(affiché sur les cartes, 1-2 phrases)</span></label>
        <input class="form-control" type="text" id="summary" name="summary" required value="<?= e($formation['summary']) ?>">
    </div>

    <div class="form-group">
        <label for="details">Détails <span class="hint">(affichés dans la fenêtre « Plus d'infos »)</span></label>
        <textarea class="form-control" id="details" name="details"><?= e($formation['details']) ?></textarea>
    </div>

    <div class="form-group">
        <label>Icône</label>
        <div class="icon-picker">
            <?php foreach ($available_icons as $icon): ?>
            <label>
                <input type="radio" name="icon" value="<?= e($icon) ?>" <?= $formation['icon'] === $icon ? 'checked' : '' ?>>
                <?php render_icon($icon, 'icon'); ?>
            </label>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="depth_label">Profondeur affichée <span class="hint">(ex. "Piscine", "6 m", "20 m", "60 m")</span></label>
            <input class="form-control" type="text" id="depth_label" name="depth_label" value="<?= e($formation['depth_label']) ?>" placeholder="20 m">
        </div>
        <div class="form-group">
            <label for="sort_order">Ordre d'affichage <span class="hint">(profondeur croissante recommandée)</span></label>
            <input class="form-control" type="number" id="sort_order" name="sort_order" value="<?= (int) $formation['sort_order'] ?>">
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= $id ? 'Enregistrer' : 'Ajouter' ?></button>
        <a href="formations.php" class="btn btn-outline">Annuler</a>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
