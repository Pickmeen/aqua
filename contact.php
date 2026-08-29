<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Contact — ' . get_content('site_name', 'Plongée Carpentras');
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero page-hero-sm">
    <div class="container">
        <p class="eyebrow">Une question&nbsp;?</p>
        <h1>Contactez-nous</h1>
        <p class="section-lead">Le club vous accueille à la piscine municipale de Carpentras, sur les créneaux d'entraînement.</p>
    </div>
</section>

<section class="section" data-reveal>
    <div class="container contact-layout">
        <div class="contact-info">
            <div class="contact-item">
                <?php render_icon('pin', 'icon icon-lg'); ?>
                <div>
                    <h3>Adresse</h3>
                    <span><?= e(get_content('contact_address')) ?></span>
                </div>
            </div>
            <div class="contact-item">
                <?php render_icon('mail', 'icon icon-lg'); ?>
                <div>
                    <h3>Email</h3>
                    <span><a href="mailto:<?= e(get_content('contact_email')) ?>"><?= e(get_content('contact_email')) ?></a></span>
                </div>
            </div>
            <div class="contact-item">
                <?php render_icon('facebook', 'icon icon-lg'); ?>
                <div>
                    <h3>Facebook</h3>
                    <span><a href="<?= e(get_content('facebook_url')) ?>" target="_blank" rel="noopener">Plongée Carpentras</a></span>
                </div>
            </div>
        </div>

        <div class="contact-map-card">
            <?php render_icon('compass', 'icon icon-xl'); ?>
            <h3>Nous trouver</h3>
            <p><?= e(get_content('contact_address')) ?></p>
            <a class="btn btn-outline"
               href="https://www.google.com/maps/search/?api=1&query=<?= urlencode(get_content('contact_address')) ?>"
               target="_blank" rel="noopener">
                Ouvrir dans Google Maps
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
