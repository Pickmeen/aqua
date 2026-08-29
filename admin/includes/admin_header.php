<?php
/**
 * Layout commun du backoffice. Variables optionnelles avant l'include :
 *   $page_title   -> titre de l'onglet et fil d'ariane
 *   $active_nav   -> clé du menu actif (dashboard, events, formations, pricing, content, account)
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../../includes/icons.php';
require_login();

$page_title = $page_title ?? 'Administration';
$active_nav = $active_nav ?? '';
$flash = get_flash();

$nav = [
    'dashboard'  => ['label' => 'Tableau de bord', 'href' => 'index.php', 'icon' => 'compass'],
    'events'     => ['label' => 'Calendrier',       'href' => 'events.php', 'icon' => 'clock'],
    'formations' => ['label' => 'Formations',        'href' => 'formations.php', 'icon' => 'fin'],
    'pricing'    => ['label' => 'Tarifs',            'href' => 'pricing.php', 'icon' => 'medal'],
    'documents'  => ['label' => 'Documents (PDF)',  'href' => 'documents.php', 'icon' => 'arrow-down'],
    'content'    => ['label' => 'Contenu du site',   'href' => 'content.php', 'icon' => 'edit'],
    'account'    => ['label' => 'Mon compte',        'href' => 'account.php', 'icon' => 'bubbles'],
];
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($page_title) ?> — Administration Plongée Carpentras</title>
<meta name="robots" content="noindex, nofollow">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">
<div class="admin-shell">
    <aside class="admin-sidebar">
        <a href="index.php" class="admin-brand">
            <?php render_icon('bubbles', 'icon'); ?>
            <span>Administration</span>
        </a>
        <nav class="admin-nav">
            <?php foreach ($nav as $key => $item): ?>
            <a href="<?= e($item['href']) ?>" class="<?= $active_nav === $key ? 'active' : '' ?>">
                <?php render_icon($item['icon'], 'icon'); ?>
                <span><?= e($item['label']) ?></span>
            </a>
            <?php endforeach; ?>
        </nav>
        <a href="logout.php" class="admin-logout">
            <?php render_icon('logout', 'icon'); ?>
            <span>Déconnexion</span>
        </a>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <h1><?= e($page_title) ?></h1>
            <a href="../index.php" target="_blank" class="btn btn-outline btn-sm">Voir le site</a>
        </header>

        <?php if ($flash): ?>
        <div class="admin-flash admin-flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>

        <div class="admin-content">
