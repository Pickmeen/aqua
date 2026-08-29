<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);
$pricing = ['label' => '', 'detail' => '', 'price' => '', 'sort_order' => 0];
$errors = [];

if ($id) {
    $stmt = get_pdo()->prepare('SELECT * FROM pricing WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        set_flash('error', 'Ligne tarifaire introuvable.');
        header('Location: pricing.php');
        exit;
    }
    $pricing = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $pricing['label'] = trim($_POST['label'] ?? '');
    $pricing['detail'] = trim($_POST['detail'] ?? '') ?: null;
    $pricing['price'] = trim($_POST['price'] ?? '');
    $pricing['sort_order'] = (int) ($_POST['sort_order'] ?? 0);

    if ($pricing['label'] === '') {
        $errors[] = 'Le libellé de l\'adhésion est obligatoire.';
    }
    if ($pricing['price'] === '') {
        $errors[] = 'Le prix total est obligatoire.';
    }

    if (!$errors) {
        if ($id) {
            $stmt = get_pdo()->prepare('UPDATE pricing SET label=?, detail=?, price=?, sort_order=? WHERE id=?');
            $stmt->execute([$pricing['label'], $pricing['detail'], $pricing['price'], $pricing['sort_order'], $id]);
            set_flash('success', 'Ligne tarifaire mise à jour.');
        } else {
            $stmt = get_pdo()->prepare('INSERT INTO pricing (label, detail, price, sort_order) VALUES (?, ?, ?, ?)');
            $stmt->execute([$pricing['label'], $pricing['detail'], $pricing['price'], $pricing['sort_order']]);
            set_flash('success', 'Ligne tarifaire ajoutée.');
        }
        header('Location: pricing.php');
        exit;
    }
}

$page_title = 'Ligne tarifaire';
$active_nav = 'pricing';
require_once __DIR__ . '/includes/admin_header.php';
?>

<a href="pricing.php" style="color:var(--depth-mid); font-weight:700; font-size:.88rem; display:inline-block; margin-bottom:1.2rem;">&larr; Retour aux tarifs</a>

<form class="admin-form" method="post" novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= (int) $id ?>">

    <?php foreach ($errors as $err): ?>
        <p class="admin-flash admin-flash-error"><?= e($err) ?></p>
    <?php endforeach; ?>

    <div class="form-group">
        <label for="label">Adhésion</label>
        <input class="form-control" type="text" id="label" name="label" required value="<?= e($pricing['label']) ?>" placeholder="Adhésion adulte (+16 ans) + formation (N1/N2)">
    </div>

    <div class="form-group">
        <label for="detail">Détail du calcul <span class="hint">(optionnel)</span></label>
        <input class="form-control" type="text" id="detail" name="detail" value="<?= e($pricing['detail']) ?>" placeholder="48,50 € + 120 € + 66 €">
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="price">Prix total</label>
            <input class="form-control" type="text" id="price" name="price" required value="<?= e($pricing['price']) ?>" placeholder="234,50 €">
        </div>
        <div class="form-group">
            <label for="sort_order">Ordre d'affichage</label>
            <input class="form-control" type="number" id="sort_order" name="sort_order" value="<?= (int) $pricing['sort_order'] ?>">
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= $id ? 'Enregistrer' : 'Ajouter' ?></button>
        <a href="pricing.php" class="btn btn-outline">Annuler</a>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
