<?php require_once __DIR__ . '/functions.php'; require_once __DIR__ . '/icons.php'; ?>
</main>

<footer class="site-footer">
    <div class="footer-wave" aria-hidden="true">
        <svg viewBox="0 0 1200 60" preserveAspectRatio="none"><path d="M0,30 C200,60 400,0 600,30 C800,60 1000,0 1200,30 L1200,0 L0,0 Z"></path></svg>
    </div>
    <div class="footer-content">
        <div class="footer-section">
            <h3 class="brand-text footer-brand"><?= e(get_content('site_name', 'Plongée Carpentras')) ?></h3>
            <p>
                <?= e(get_content('association_name')) ?><br>
                <?= e(get_content('contact_address')) ?><br>
                <a href="mailto:<?= e(get_content('contact_email')) ?>"><?= e(get_content('contact_email')) ?></a>
            </p>
        </div>

        <div class="footer-section">
            <h3>Suivez-nous</h3>
            <a href="<?= e(get_content('facebook_url')) ?>" target="_blank" rel="noopener" class="social-icon" aria-label="Facebook">
                <?php render_icon('facebook', 'icon'); ?>
            </a>
        </div>

        <div class="footer-section">
            <h3>Informations</h3>
            <p><a href="#" id="mentions-legales-link">Mentions légales</a></p>
        </div>

        <div class="footer-section">
            <p class="footer-copy"><?= e(get_content('footer_text')) ?></p>
        </div>
    </div>
</footer>

<button id="back-to-top" class="back-to-top" aria-label="Remonter en haut de page">
    <?php render_icon('arrow-down', 'icon icon-rotate'); ?>
</button>

<!-- POPUP MENTIONS LÉGALES -->
<div id="mentions-legales-popup" class="popup" role="dialog" aria-modal="true" aria-labelledby="mentions-title">
    <div class="popup-content">
        <button class="close-popup" aria-label="Fermer">&times;</button>
        <h2 id="mentions-title">Mentions légales</h2>
        <p><?= nl2br(e(get_content('mentions_legales'))) ?></p>
    </div>
</div>

<script src="assets/js/main.js"></script>
</body>
</html>
