<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = get_content('seo_title', get_content('site_name', 'Plongée Carpentras'));
$page_description = get_content('seo_description');
$load_three = true;
require_once __DIR__ . '/includes/header.php';

$formations = get_formations();
$pricing = get_pricing();
$documents = get_documents();
?>

<div class="ocean-backdrop" aria-hidden="true">
    <span class="ray ray-1"></span>
    <span class="ray ray-2"></span>
</div>
<div class="depth-rail" aria-hidden="true">
    <div class="depth-rail-ticks"><span>0 m</span><span>20 m</span><span>60 m</span></div>
    <div class="depth-rail-track">
        <div class="depth-rail-fill"></div>
        <div class="depth-rail-dot"></div>
    </div>
</div>

<!-- HERO -->
<section class="hero" id="hero">
    <canvas id="hero-3d" class="hero-3d-canvas" aria-hidden="true"></canvas>
    <div class="hero-content">
        <p class="eyebrow">Carpentras &middot; Vaucluse &middot; FFESSM</p>
        <h1><?= e(get_content('hero_title')) ?></h1>
        <p class="hero-subtitle"><?= e(get_content('hero_subtitle')) ?></p>
        <div class="hero-actions">
            <a href="#formations" class="btn btn-primary">
                <?php render_icon('first-dive', 'icon'); ?>
                <?= e(get_content('hero_cta_baptism', 'Baptême de plongée')) ?>
            </a>
            <a href="inscription.php" class="btn btn-ghost">
                <?= e(get_content('hero_cta_join', 'Nous rejoindre')) ?>
            </a>
        </div>
    </div>
    <div class="hero-scroll-cue">
        <span>Découvrir</span>
        <?php render_icon('arrow-down', 'icon'); ?>
    </div>
</section>

<!-- LE CLUB EN 3 CHIFFRES -->
<section class="section section-tight" id="club">
    <div class="container">
        <h2 class="section-title" data-reveal>Le club en 3 chiffres</h2>
        <div class="stats-grid">
            <div class="stat-card" data-reveal>
                <div class="stat-value"><?= e(get_content('stat_1_value')) ?></div>
                <div class="stat-label"><?= e(get_content('stat_1_label')) ?></div>
            </div>
            <div class="stat-card" data-reveal>
                <div class="stat-value"><?= e(get_content('stat_2_value')) ?></div>
                <div class="stat-label"><?= e(get_content('stat_2_label')) ?></div>
            </div>
            <div class="stat-card" data-reveal>
                <div class="stat-value"><?= e(get_content('stat_3_value')) ?></div>
                <div class="stat-label"><?= e(get_content('stat_3_label')) ?></div>
            </div>
        </div>
        <p class="club-intro" data-reveal><?= e(get_content('club_intro')) ?></p>
    </div>
</section>

<!-- FORMATIONS -->
<section class="section" id="formations">
    <div class="container">
        <h2 class="section-title" data-reveal>Nos formations</h2>
        <p class="section-lead on-dark" data-reveal>Du baptême aux brevets d'encadrement, classées par profondeur croissante. Cliquez une carte pour le détail.</p>

        <div class="grid grid-4">
            <?php foreach ($formations as $formation): ?>
            <div class="formation-card"
                 role="button" tabindex="0"
                 data-reveal
                 data-title="<?= e($formation['title']) ?>"
                 data-depth="<?= e($formation['depth_label']) ?>"
                 data-details="<?= e($formation['details']) ?>">
                <div class="formation-card-top">
                    <div class="formation-icon">
                        <?php render_icon($formation['icon'], 'icon'); ?>
                        <canvas class="formation-icon-3d" aria-hidden="true"></canvas>
                    </div>
                    <span class="depth-badge"><?= e($formation['depth_label']) ?></span>
                </div>
                <h3><?= e($formation['title']) ?></h3>
                <p><?= e($formation['summary']) ?></p>
                <span class="card-cta">Voir le détail <?php render_icon('chevron-right', 'icon'); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- TARIFS -->
<section class="section" id="tarifs">
    <div class="container">
        <h2 class="section-title" data-reveal>Inscriptions &amp; tarifs</h2>
        <p class="section-lead on-dark" data-reveal>Adhésion annuelle, licence FFESSM et formation associées selon votre profil.</p>

        <div class="price-table-wrap" data-reveal>
            <table class="price-table">
                <thead>
                    <tr>
                        <th>Adhésion</th>
                        <th>Licence + cotisations + formation</th>
                        <th>Prix total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pricing as $row): ?>
                    <tr>
                        <td data-label="Adhésion"><?= e($row['label']) ?></td>
                        <td data-label="Détail"><?= e($row['detail']) ?></td>
                        <td data-label="Prix total" class="total-price"><?= e($row['price']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="note" data-reveal><?= e(get_content('pricing_note')) ?></p>

        <div class="center-cta" data-reveal>
            <a href="inscription.php" class="btn btn-primary">Voir les étapes d'inscription</a>
        </div>
    </div>
</section>

<!-- CONTACT -->
<section class="section" id="contact">
    <div class="container">
        <h2 class="section-title" data-reveal>Contactez-nous</h2>
        <p class="section-lead on-dark" data-reveal>Le club vous accueille à la piscine municipale de Carpentras, sur les créneaux d'entraînement.</p>

        <div class="contact-layout">
            <div class="contact-info">
                <div class="contact-item" data-reveal>
                    <?php render_icon('pin', 'icon icon-lg'); ?>
                    <div>
                        <h3>Adresse</h3>
                        <span><?= e(get_content('contact_address')) ?></span>
                    </div>
                </div>
                <div class="contact-item" data-reveal>
                    <?php render_icon('mail', 'icon icon-lg'); ?>
                    <div>
                        <h3>Email</h3>
                        <span><a href="mailto:<?= e(get_content('contact_email')) ?>"><?= e(get_content('contact_email')) ?></a></span>
                    </div>
                </div>
                <div class="contact-item" data-reveal>
                    <?php render_icon('facebook', 'icon icon-lg'); ?>
                    <div>
                        <h3>Facebook</h3>
                        <span><a href="<?= e(get_content('facebook_url')) ?>" target="_blank" rel="noopener">Plongée Carpentras</a></span>
                    </div>
                </div>
            </div>

            <div class="contact-map-card" data-reveal>
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
    </div>
</section>

<!-- OVERLAY + PANNEAU DÉTAIL FORMATION -->
<div class="panel-overlay"></div>
<aside class="formation-panel" aria-hidden="true">
    <button class="close-panel" aria-label="Fermer">&times;</button>
    <div class="formation-icon"></div>
    <h2 id="panel-title"></h2>
    <span class="depth-badge" id="panel-depth"></span>
    <p id="panel-description"></p>
    <a href="#contact" class="btn btn-primary">Nous contacter pour cette formation</a>
</aside>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
