<?php
/**
 * En-tête commun : <head>, balises meta, navigation.
 * Variables attendues (optionnelles) avant l'include :
 *   $page_title       -> titre de l'onglet
 *   $page_description -> meta description
 */
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/icons.php';

$page_title = $page_title ?? get_content('site_name', 'Plongée Carpentras');
$page_description = $page_description ?? 'Club Subaquatique du Comtat Venaissin — plongée sous-marine à Carpentras : formations, baptêmes, sorties et calendrier du club.';
$current_page = basename($_SERVER['SCRIPT_NAME']);

$nav_items = [
    'index.php'       => 'Accueil',
    'calendrier.php'   => 'Calendrier',
    'formations.php'   => 'Formations',
    'inscription.php'  => 'Inscriptions & tarifs',
    'contact.php'      => 'Contact',
];
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($page_title) ?></title>
<meta name="description" content="<?= e($page_description) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22><text y=%2220%22 font-size=%2220%22>🤿</text></svg>">
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-YSK54J1WP0"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'G-YSK54J1WP0');
</script>
</head>
<body>
<a class="skip-link" href="#main">Aller au contenu</a>

<header class="site-header" id="site-header">
    <nav class="navbar">
        <a href="index.php" class="brand">
            <span class="brand-mark" aria-hidden="true"><?php render_icon('bubbles', 'icon'); ?></span>
            <span class="brand-text">Plongée Carpentras</span>
        </a>

        <button class="menu-toggle" id="menu-toggle" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="nav-links">
            <span class="menu-bar"></span>
            <span class="menu-bar"></span>
            <span class="menu-bar"></span>
        </button>

        <ul class="nav-links" id="nav-links">
            <?php foreach ($nav_items as $href => $label): ?>
            <li>
                <a href="<?= e($href) ?>" class="<?= $current_page === $href ? 'active' : '' ?>">
                    <?= e($label) ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</header>

<main id="main">
