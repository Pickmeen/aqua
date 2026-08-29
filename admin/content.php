<?php
$page_title = 'Contenu du site';
$active_nav = 'content';
require_once __DIR__ . '/includes/admin_header.php';

$fields = [
    'site_name'       => ['label' => 'Nom du site', 'type' => 'text'],
    'hero_title'      => ['label' => 'Titre principal (page d\'accueil)', 'type' => 'text'],
    'hero_subtitle'   => ['label' => 'Sous-titre (page d\'accueil)', 'type' => 'text'],
    'hero_cta_text'   => ['label' => 'Texte du bouton Facebook', 'type' => 'text'],
    'facebook_url'    => ['label' => 'Lien Facebook', 'type' => 'text'],
    'contact_address' => ['label' => 'Adresse', 'type' => 'text'],
    'contact_email'   => ['label' => 'Email de contact', 'type' => 'text'],
    'association_name'=> ['label' => 'Nom de l\'association', 'type' => 'text'],
    'footer_text'     => ['label' => 'Texte de copyright (pied de page)', 'type' => 'text'],
    'pricing_note'    => ['label' => 'Note sous le tableau des tarifs', 'type' => 'text'],
    'mentions_legales'=> ['label' => 'Mentions légales', 'type' => 'textarea'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $stmt = get_pdo()->prepare(
        'INSERT INTO site_content (content_key, content_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE content_value = VALUES(content_value)'
    );

    foreach (array_keys($fields) as $key) {
        $value = trim($_POST[$key] ?? '');
        $stmt->execute([$key, $value]);
    }

    set_flash('success', 'Contenu du site mis à jour.');
    header('Location: content.php');
    exit;
}

$values = [];
foreach (array_keys($fields) as $key) {
    $values[$key] = get_content($key);
}
?>

<form class="admin-form" method="post" style="max-width:720px;" novalidate>
    <?= csrf_field() ?>

    <?php foreach ($fields as $key => $field): ?>
    <div class="form-group">
        <label for="<?= e($key) ?>"><?= e($field['label']) ?></label>
        <?php if ($field['type'] === 'textarea'): ?>
            <textarea class="form-control" id="<?= e($key) ?>" name="<?= e($key) ?>" style="min-height:180px;"><?= e($values[$key]) ?></textarea>
        <?php else: ?>
            <input class="form-control" type="text" id="<?= e($key) ?>" name="<?= e($key) ?>" value="<?= e($values[$key]) ?>">
        <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
