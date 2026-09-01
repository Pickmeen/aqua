<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$groups = [
    'Page d\'accueil' => [
        'hero_eyebrow'    => ['label' => 'Petite ligne au-dessus du titre (hero)', 'type' => 'text'],
        'hero_title'      => ['label' => 'Titre principal (hero)', 'type' => 'text'],
        'hero_subtitle'   => ['label' => 'Sous-titre (hero)', 'type' => 'textarea'],
        'hero_cta_baptism'=> ['label' => 'Texte du bouton "Baptême"', 'type' => 'text'],
        'hero_cta_join'   => ['label' => 'Texte du bouton "Nous rejoindre"', 'type' => 'text'],
        'club_intro'      => ['label' => 'Texte de présentation du club', 'type' => 'textarea'],
    ],
    'Le club en 3 chiffres' => [
        'stat_1_value' => ['label' => 'Chiffre 1', 'type' => 'text'],
        'stat_1_label' => ['label' => 'Légende du chiffre 1', 'type' => 'text'],
        'stat_2_value' => ['label' => 'Chiffre 2', 'type' => 'text'],
        'stat_2_label' => ['label' => 'Légende du chiffre 2', 'type' => 'text'],
        'stat_3_value' => ['label' => 'Chiffre 3', 'type' => 'text'],
        'stat_3_label' => ['label' => 'Légende du chiffre 3', 'type' => 'text'],
    ],
    'Coordonnées' => [
        'site_name'        => ['label' => 'Nom du site', 'type' => 'text'],
        'association_name' => ['label' => 'Nom de l\'association', 'type' => 'text'],
        'contact_address'  => ['label' => 'Adresse', 'type' => 'text'],
        'contact_email'    => ['label' => 'Email de contact', 'type' => 'text'],
        'contact_hours'    => ['label' => 'Créneaux / horaires affichés dans la section Contact', 'type' => 'textarea'],
        'facebook_url'     => ['label' => 'Lien Facebook', 'type' => 'text'],
        'footer_text'      => ['label' => 'Texte de copyright (pied de page)', 'type' => 'text'],
        'pricing_note'     => ['label' => 'Note sous le tableau des tarifs', 'type' => 'text'],
    ],
    'Référencement (SEO)' => [
        'seo_title'       => ['label' => 'Titre SEO (balise <title>)', 'type' => 'text'],
        'seo_description' => ['label' => 'Description SEO (meta description)', 'type' => 'textarea'],
    ],
    'Mentions légales' => [
        'mentions_legales' => ['label' => 'Texte des mentions légales', 'type' => 'textarea'],
    ],
];

$fields = array_merge(...array_values($groups));

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

$page_title = 'Contenu du site';
$active_nav = 'content';
require_once __DIR__ . '/includes/admin_header.php';

$values = [];
foreach (array_keys($fields) as $key) {
    $values[$key] = get_content($key);
}
?>

<form class="admin-form" method="post" style="max-width:720px;" novalidate>
    <?= csrf_field() ?>

    <?php foreach ($groups as $groupLabel => $groupFields): ?>
        <h2 style="font-size:1.1rem; margin:1.6rem 0 1rem; color:var(--ink-900);"><?= e($groupLabel) ?></h2>
        <?php foreach ($groupFields as $key => $field): ?>
        <div class="form-group">
            <label for="<?= e($key) ?>"><?= e($field['label']) ?></label>
            <?php if ($field['type'] === 'textarea'): ?>
                <textarea class="form-control" id="<?= e($key) ?>" name="<?= e($key) ?>" style="min-height:120px;"><?= e($values[$key]) ?></textarea>
            <?php else: ?>
                <input class="form-control" type="text" id="<?= e($key) ?>" name="<?= e($key) ?>" value="<?= e($values[$key]) ?>">
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php endforeach; ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
