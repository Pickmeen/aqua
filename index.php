<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = get_content('site_name', 'Plongée Carpentras') . ' — Club de plongée sous-marine à Carpentras';
require_once __DIR__ . '/includes/header.php';

$next_event = get_next_event();
$formations_preview = array_slice(get_formations(), 0, 4);
?>

<!-- HERO -->
<section class="hero">
    <div class="hero-ocean" aria-hidden="true">
        <div class="ray ray-1"></div>
        <div class="ray ray-2"></div>
        <div class="ray ray-3"></div>
        <span class="bubble b1"></span>
        <span class="bubble b2"></span>
        <span class="bubble b3"></span>
        <span class="bubble b4"></span>
        <span class="bubble b5"></span>
        <span class="bubble b6"></span>
    </div>
    <div class="hero-content">
        <p class="eyebrow">Carpentras &middot; Vaucluse</p>
        <h1><?= e(get_content('hero_title')) ?></h1>
        <p class="hero-subtitle"><?= e(get_content('hero_subtitle')) ?></p>
        <div class="hero-actions">
            <a href="<?= e(get_content('facebook_url')) ?>" target="_blank" rel="noopener" class="btn btn-primary">
                <?php render_icon('facebook', 'icon'); ?>
                <?= e(get_content('hero_cta_text')) ?>
            </a>
            <a href="formations.php" class="btn btn-ghost">Découvrir nos formations</a>
        </div>
    </div>
    <div class="hero-wave" aria-hidden="true">
        <svg viewBox="0 0 1200 80" preserveAspectRatio="none"><path d="M0,40 C200,80 400,0 600,40 C800,80 1000,0 1200,40 L1200,80 L0,80 Z"></path></svg>
    </div>
</section>

<!-- ATOUTS -->
<section class="section values" data-reveal>
    <div class="container">
        <h2 class="section-title">La plongée, en toute confiance</h2>
        <p class="section-lead">Un club associatif à taille humaine, des encadrants diplômés et des séances en piscine comme en mer, ouvertes à tous les âges.</p>

        <div class="value-grid">
            <div class="value-card">
                <?php render_icon('medal', 'icon icon-lg'); ?>
                <h3>Encadrement qualifié</h3>
                <p>Des moniteurs et initiateurs diplômés FFESSM vous accompagnent à chaque étape, du baptême aux niveaux les plus avancés.</p>
            </div>
            <div class="value-card">
                <?php render_icon('fin', 'icon icon-lg'); ?>
                <h3>De la piscine à la mer</h3>
                <p>Entraînements réguliers à la piscine municipale et sorties encadrées en mer pour progresser en toute sécurité.</p>
            </div>
            <div class="value-card">
                <?php render_icon('child', 'icon icon-lg'); ?>
                <h3>Pour tous les âges</h3>
                <p>Des sections dédiées aux enfants dès 10 ans, aux ados et aux adultes, jusqu'aux formations d'encadrement.</p>
            </div>
            <div class="value-card">
                <?php render_icon('bubbles', 'icon icon-lg'); ?>
                <h3>Esprit club</h3>
                <p>Une association conviviale où l'on partage la passion du monde sous-marin, en dehors de toute compétition.</p>
            </div>
        </div>
    </div>
</section>

<!-- PROCHAIN EVENEMENT -->
<?php if ($next_event): ?>
<section class="section next-event" data-reveal>
    <div class="container next-event-inner">
        <div class="next-event-icon"><?php render_icon('clock', 'icon icon-lg'); ?></div>
        <div class="next-event-text">
            <p class="eyebrow">Prochain rendez-vous</p>
            <h2><?= e($next_event['title']) ?></h2>
            <p class="next-event-date">
                <?= e(format_event_date($next_event['event_date'])) ?><?= $next_event['event_time'] ? ' &middot; ' . e(substr($next_event['event_time'], 0, 5)) : '' ?>
            </p>
            <?php if (!empty($next_event['description'])): ?>
                <p><?= e($next_event['description']) ?></p>
            <?php endif; ?>
        </div>
        <a href="calendrier.php" class="btn btn-primary">Voir le calendrier</a>
    </div>
</section>
<?php endif; ?>

<!-- APERCU FORMATIONS -->
<section class="section formations-preview" data-reveal>
    <div class="container">
        <h2 class="section-title">Nos formations</h2>
        <p class="section-lead">Du baptême de plongée jusqu'au niveau 4, en passant par les brevets d'encadrement.</p>

        <div class="grid grid-4">
            <?php foreach ($formations_preview as $formation): ?>
            <div class="card">
                <div class="card-icon"><?php render_icon($formation['icon'], 'icon icon-lg'); ?></div>
                <h3><?= e($formation['title']) ?></h3>
                <p><?= e($formation['summary']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="center-cta">
            <a href="formations.php" class="btn btn-outline">Voir toutes les formations</a>
        </div>
    </div>
</section>

<!-- CTA FINALE -->
<section class="section cta-band" data-reveal>
    <div class="container cta-band-inner">
        <div>
            <h2>Envie de nous rejoindre&nbsp;?</h2>
            <p>Contactez-nous ou passez directement nous voir à la piscine municipale de Carpentras.</p>
        </div>
        <div class="hero-actions">
            <a href="contact.php" class="btn btn-primary">Nous contacter</a>
            <a href="inscription.php" class="btn btn-ghost">Tarifs &amp; inscriptions</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
