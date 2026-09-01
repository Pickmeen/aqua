<?php
/**
 * Pied de page commun + éléments flottants (HUD, barre d'actions mobile,
 * bouton de retour en haut) et dialogues natifs.
 */
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/icons.php';

$footer_next = $footer_next ?? get_next_event();
?>
</main>

<?php if (!empty($show_hud)): ?>
<!-- Ordinateur de plongée : la « profondeur » suit l'avancée dans la page. -->
<aside class="dive-hud" id="dive-hud" data-max-depth="60" aria-label="Repère de progression dans la page">
    <button type="button" class="hud-close" id="hud-close" aria-label="Masquer le repère de progression">
        <?php render_icon('close'); ?>
    </button>
    <!-- Les compteurs sont purement décoratifs : on les retire de l'arbre
         d'accessibilité pour ne rien annoncer à chaque pixel de scroll. -->
    <div aria-hidden="true">
        <div class="hud-label">Profondeur</div>
        <div class="hud-depth" id="hud-depth">0<span>m</span></div>
    </div>
    <div class="hud-bar" id="hud-bar" aria-hidden="true"></div>
    <div class="hud-section" id="hud-section" aria-hidden="true">Surface</div>
    <?php if ($footer_next): ?>
    <div class="hud-next">
        <?php render_icon('calendar'); ?>
        <span>Prochain RDV <b data-countdown="<?= e($footer_next['event_date']) ?>"><?= e(countdown_label($footer_next['event_date'])) ?></b></span>
    </div>
    <?php endif; ?>
</aside>
<?php endif; ?>

<footer class="site-footer">
    <div class="wrap">
        <div class="footer-grid">
            <div>
                <p class="brand-text">
                    <?= e(get_content('site_name', 'Plongée Carpentras')) ?>
                    <small>Club affilié FFESSM</small>
                </p>
                <p style="margin-block-start:1rem">
                    <?= e(get_content('association_name')) ?><br>
                    <?= e(get_content('contact_address')) ?><br>
                    <a href="mailto:<?= e(get_content('contact_email')) ?>"><?= e(get_content('contact_email')) ?></a>
                </p>
            </div>

            <div>
                <h3>Le club</h3>
                <div class="footer-links">
                    <a href="index.php#formations">Nos formations</a>
                    <a href="index.php#tarifs">Inscriptions &amp; tarifs</a>
                    <a href="calendrier.php">Calendrier de la saison</a>
                    <a href="inscription.php">Comment nous rejoindre</a>
                </div>
            </div>

            <div>
                <h3>Nous suivre</h3>
                <a href="<?= e(get_content('facebook_url')) ?>" target="_blank" rel="noopener"
                   class="social" aria-label="Le club sur Facebook">
                    <?php render_icon('facebook'); ?>
                </a>
                <p style="margin-block-start:1rem">
                    Toute l'actualité du club, les photos de sorties et les
                    créneaux exceptionnels sont publiés sur notre page.
                </p>
            </div>
        </div>

        <div class="footer-bottom">
            <span><?= e(get_content('footer_text')) ?></span>
            <span>
                <a href="#" data-dialog-open="legal-dialog">Mentions légales</a>
            </span>
            <span class="footer-tech">Site sans traceur publicitaire &middot; accessible au clavier</span>
        </div>
    </div>
</footer>

<!-- Barre d'actions permanente sur mobile : les deux gestes les plus utiles
     restent toujours à portée de pouce. -->
<nav class="action-bar" aria-label="Actions rapides">
    <a href="index.php#contact" class="btn btn-sm">
        <?php render_icon('first-dive'); ?> Baptême
    </a>
    <a href="inscription.php" class="btn btn-sm btn-ghost">
        <?php render_icon('users'); ?> Nous rejoindre
    </a>
</nav>

<button type="button" id="to-top" class="to-top" aria-label="Revenir en haut de la page">
    <?php render_icon('arrow-up'); ?>
</button>

<dialog id="legal-dialog" aria-labelledby="legal-title">
    <button type="button" class="dialog-close" data-close aria-label="Fermer"><?php render_icon('close'); ?></button>
    <div class="dialog-body">
        <h2 id="legal-title">Mentions légales</h2>
        <div class="dialog-scroll">
            <p><?= nl2br(e(get_content('mentions_legales'))) ?></p>
        </div>
    </div>
</dialog>

<script src="assets/js/main.js" defer></script>
<?php if (!empty($hero_scene)): ?>
<script src="assets/js/hero-scene.js" defer></script>
<?php endif; ?>
</body>
</html>
