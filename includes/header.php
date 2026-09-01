<?php
/**
 * En-tête commun : <head>, balises meta, navigation.
 * Variables optionnelles à définir avant l'include :
 *   $page_title       -> titre de l'onglet
 *   $page_description -> meta description
 *   $show_hud         -> affiche l'ordinateur de plongée (page d'accueil)
 *   $hero_scene       -> charge le décor 3D du hero
 */
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/icons.php';

$page_title = $page_title ?? get_content('site_name', 'Plongée Carpentras');
$page_description = $page_description ?? 'Club Subaquatique du Comtat Venaissin — plongée sous-marine à Carpentras : formations, baptêmes, sorties et calendrier du club.';
$current_page = basename($_SERVER['SCRIPT_NAME']);
$canonical_path = $current_page === 'index.php' ? '' : $current_page;
$site_base_url = 'https://www.plongeecarpentras.fr/';

$nav_items = [
    'index.php#formations' => 'Formations',
    'index.php#tarifs'     => 'Tarifs',
    'calendrier.php'       => 'Calendrier',
    'inscription.php'      => 'Nous rejoindre',
    'index.php#contact'    => 'Contact',
];
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= e($page_title) ?></title>
<meta name="description" content="<?= e($page_description) ?>">
<?php if (is_preprod()): ?>
<meta name="robots" content="noindex, nofollow">
<?php endif; ?>
<meta name="theme-color" content="#f4f8fa" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#061620" media="(prefers-color-scheme: dark)">
<link rel="canonical" href="<?= e($site_base_url . $canonical_path) ?>">
<meta property="og:type" content="website">
<meta property="og:locale" content="fr_FR">
<meta property="og:site_name" content="<?= e(get_content('site_name', 'Plongée Carpentras')) ?>">
<meta property="og:title" content="<?= e($page_title) ?>">
<meta property="og:description" content="<?= e($page_description) ?>">
<meta property="og:url" content="<?= e($site_base_url . $canonical_path) ?>">
<meta name="twitter:card" content="summary">

<?php if ($current_page === 'index.php'): ?>
<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'SportsActivityLocation',
    'name' => get_content('association_name'),
    'alternateName' => get_content('site_name'),
    'url' => $site_base_url,
    'sport' => 'Plongée sous-marine',
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => get_content('contact_address'),
        'addressLocality' => 'Carpentras',
        'addressRegion' => 'Vaucluse',
        'postalCode' => '84200',
        'addressCountry' => 'FR',
    ],
    'email' => get_content('contact_email'),
    'sameAs' => array_filter([get_content('facebook_url')]),
    'memberOf' => ['@type' => 'Organization', 'name' => 'FFESSM'],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
</script>
<?php endif; ?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22><text y=%2220%22 font-size=%2220%22>🤿</text></svg>">

<!-- Thème appliqué avant le premier rendu : pas de flash blanc au chargement
     pour les visiteurs qui ont choisi le mode sombre. -->
<script>
(function () {
    var root = document.documentElement;

    try {
        var saved = localStorage.getItem('cscv-theme');
        if (saved === 'dark' || saved === 'light') { root.dataset.theme = saved; }
    } catch (e) { /* stockage indisponible : on garde la préférence système */ }

    // Les apparitions au scroll sont natives dans les navigateurs récents.
    // Ailleurs, on passe par le repli JS : on masque dès maintenant pour
    // éviter le clignotement — mais on démasque tout si main.js n'a pas
    // pris la main au bout de 3 s (script bloqué, erreur réseau...).
    var native = window.CSS && CSS.supports && CSS.supports('animation-timeline', 'view()');
    if (!native) {
        root.classList.add('reveal-js');
        setTimeout(function () {
            if (!root.dataset.revealReady) { root.classList.remove('reveal-js'); }
        }, 3000);
    }
})();
</script>

<?php if (!is_preprod()): ?>
<!-- Google tag (gtag.js) — jamais chargé en préproduction -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-YSK54J1WP0"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'G-YSK54J1WP0');
</script>
<?php endif; ?>
</head>
<body>
<a class="skip-link" href="#main">Aller au contenu</a>

<?php if (is_preprod()): ?>
<p class="env-banner" role="status">
    <b>Préproduction</b> — version de test, non indexée par Google.
    <?php if (preprod_is_readonly()): ?>
        Contenu réel du site, <strong>backoffice verrouillé</strong> : rien
        de ce que vous faites ici ne modifie le site en ligne.
    <?php endif; ?>
    Le vrai site reste <a href="<?= e($site_base_url) ?>">plongeecarpentras.fr</a>.
</p>
<?php endif; ?>

<header class="site-header" id="site-header">
    <nav class="navbar" aria-label="Navigation principale">
        <a href="index.php" class="brand">
            <span class="brand-mark" aria-hidden="true"><?php render_icon('bubbles'); ?></span>
            <span class="brand-text">
                Plongée Carpentras
                <small>CSCV &middot; FFESSM</small>
            </span>
        </a>

        <ul class="nav-links" id="nav-links">
            <?php foreach ($nav_items as $href => $label): ?>
            <li>
                <a href="<?= e($href) ?>"<?= (strpos($href, '#') === false && $current_page === $href) ? ' class="active" aria-current="page"' : '' ?>>
                    <?= e($label) ?>
                </a>
            </li>
            <?php endforeach; ?>
            <li class="nav-cta"><a href="index.php#contact" class="btn btn-sm">Baptême de plongée</a></li>
        </ul>

        <div class="nav-actions">
            <button type="button" class="icon-btn theme-toggle" id="theme-toggle" aria-label="Changer de thème" aria-pressed="false">
                <?php render_icon('sun', 'icon icon-sun'); ?>
                <?php render_icon('moon', 'icon icon-moon'); ?>
            </button>
            <button type="button" class="icon-btn menu-toggle" id="menu-toggle"
                    aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="nav-links">
                <?php render_icon('menu'); ?>
            </button>
        </div>
    </nav>
</header>

<main id="main">
