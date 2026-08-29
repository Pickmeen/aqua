<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Formations — ' . get_content('site_name', 'Plongée Carpentras');
require_once __DIR__ . '/includes/header.php';

$formations = get_formations();
?>

<section class="page-hero page-hero-sm">
    <div class="container">
        <p class="eyebrow">Progresser à votre rythme</p>
        <h1>Nos formations</h1>
        <p class="section-lead">Du premier baptême aux brevets d'encadrement, un parcours complet encadré par des moniteurs diplômés FFESSM.</p>
    </div>
</section>

<section class="section" data-reveal>
    <div class="container">
        <div class="grid grid-4">
            <?php foreach ($formations as $formation): ?>
            <div class="card">
                <div class="card-icon"><?php render_icon($formation['icon'], 'icon icon-lg'); ?></div>
                <h2><?= e($formation['title']) ?></h2>
                <p><?= e($formation['summary']) ?></p>
                <?php if (!empty($formation['details'])): ?>
                <button type="button" class="info-btn" data-title="<?= e($formation['title']) ?>" data-description="<?= e($formation['details']) ?>">
                    Plus d'infos
                </button>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- POPUP PLUS D'INFOS -->
<div id="info-popup" class="popup" role="dialog" aria-modal="true">
    <div class="popup-content">
        <button class="close-popup" aria-label="Fermer">&times;</button>
        <h2 id="popup-title"></h2>
        <p id="popup-description"></p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
