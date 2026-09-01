<?php
require_once __DIR__ . '/includes/functions.php';

$page_title = get_content('seo_title', get_content('site_name', 'Plongée Carpentras'));
$page_description = get_content('seo_description');
$show_hud = true;      // ordinateur de plongée flottant
$hero_scene = true;    // décor 3D du hero

require_once __DIR__ . '/includes/header.php';

$formations = get_formations();
$pricing    = get_pricing();
$next_event = get_next_event();

// Filtres proposés au-dessus de la grille des formations.
$filters = [
    'all'         => 'Toutes',
    'debutant'    => 'Je débute',
    'piscine'     => 'En piscine',
    'mer'         => 'En mer',
    'encadrement' => 'Encadrement',
];
?>

<!-- =============================================================
     HERO — ce que fait le club, et le prochain rendez-vous
     ============================================================= -->
<section class="hero" data-hud-label="Surface">
    <canvas id="hero-canvas" class="hero-canvas" aria-hidden="true"></canvas>
    <div class="wrap hero-inner">
        <div>
            <p class="eyebrow"><?= e(get_content('hero_eyebrow', 'Carpentras · Vaucluse · Club FFESSM')) ?></p>
            <h1><?= e(get_content('hero_title', 'Apprenez à plonger à Carpentras')) ?></h1>
            <p class="hero-sub"><?= e(get_content('hero_subtitle')) ?></p>

            <div class="hero-actions">
                <a href="#contact" class="btn btn-lg">
                    <?php render_icon('first-dive'); ?>
                    <?= e(get_content('hero_cta_baptism', 'Baptême de plongée')) ?>
                </a>
                <a href="inscription.php" class="btn btn-lg btn-ghost">
                    <?= e(get_content('hero_cta_join', 'Nous rejoindre')) ?>
                </a>
            </div>

            <p class="hero-trust">
                <span><?php render_icon('shield'); ?> Encadrants diplômés FFESSM</span>
                <span><?php render_icon('users'); ?> Dès 10 ans</span>
                <span><?php render_icon('pin'); ?> Piscine municipale</span>
            </p>
        </div>

        <!-- La question n°1 des visiteurs : « c'est quand la prochaine fois ? » -->
        <div class="next-card" data-reveal>
            <div class="next-card-head">
                <p class="eyebrow" style="margin:0">Prochain rendez-vous</p>
                <?php if ($next_event): ?>
                    <span class="countdown" data-countdown="<?= e($next_event['event_date']) ?>">
                        <?= e(countdown_label($next_event['event_date'])) ?>
                    </span>
                <?php endif; ?>
            </div>

            <?php if ($next_event): ?>
                <div style="display:flex; gap:1rem; align-items:flex-start">
                    <div class="next-date" aria-hidden="true">
                        <b><?= e(date('d', strtotime($next_event['event_date']))) ?></b>
                        <span><?= e(format_month_abbrev($next_event['event_date'])) ?></span>
                    </div>
                    <div class="next-body">
                        <h3><?= e($next_event['title']) ?></h3>
                        <p class="small muted">
                            <?= e(format_event_date($next_event['event_date'])) ?><?= $next_event['event_time'] ? ' · ' . e(substr($next_event['event_time'], 0, 5)) : '' ?>
                        </p>
                        <?php if (!empty($next_event['description'])): ?>
                            <p class="small muted" style="margin-block-start:.4rem"><?= e($next_event['description']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <a href="calendrier.php" class="link-arrow">
                    Voir tout le calendrier <?php render_icon('arrow-right'); ?>
                </a>
            <?php else: ?>
                <p class="next-empty">
                    Aucune date publiée pour le moment. Le calendrier de la saison
                    est mis à jour dès que les créneaux sont fixés.
                </p>
                <a href="calendrier.php" class="link-arrow">
                    Ouvrir le calendrier <?php render_icon('arrow-right'); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- =============================================================
     PAR OÙ COMMENCER — trois portes d'entrée explicites
     ============================================================= -->
<section class="section section-tint" id="commencer" data-hud-label="Par où commencer">
    <div class="wrap">
        <div class="section-head" data-reveal>
            <p class="eyebrow">Par où commencer&nbsp;?</p>
            <h2>Trois façons d'entrer dans l'eau</h2>
            <p class="lead">Choisissez la situation qui vous ressemble : chaque piste
               mène directement à l'information dont vous avez besoin.</p>
        </div>

        <div class="paths">
            <article class="path-card" data-reveal>
                <span class="path-num">01</span>
                <h3>Je n'ai jamais plongé</h3>
                <p>Le baptême se fait en piscine, encadré par un moniteur, sans
                   aucun prérequis ni matériel personnel. Comptez une heure.</p>
                <a href="#contact" class="link-arrow">Demander un baptême <?php render_icon('arrow-right'); ?></a>
            </article>

            <article class="path-card" data-reveal>
                <span class="path-num">02</span>
                <h3>Je veux me former</h3>
                <p>Du Niveau 1 au Niveau 4, les formations fédérales sont assurées
                   au club, en piscine l'hiver et en mer à la belle saison.</p>
                <a href="#formations" class="link-arrow">Voir les formations <?php render_icon('arrow-right'); ?></a>
            </article>

            <article class="path-card" data-reveal>
                <span class="path-num">03</span>
                <h3>Je plonge déjà, je cherche un club</h3>
                <p>Adhésion, licence FFESSM, créneaux d'entraînement et sorties mer :
                   tout est détaillé, tarifs compris.</p>
                <a href="inscription.php" class="link-arrow">Rejoindre le club <?php render_icon('arrow-right'); ?></a>
            </article>
        </div>
    </div>
</section>

<!-- =============================================================
     LE CLUB EN CHIFFRES
     ============================================================= -->
<section class="section" id="club" data-hud-label="Le club">
    <div class="wrap">
        <div class="section-head" data-reveal>
            <p class="eyebrow">Le club</p>
            <h2>Le CSCV en trois chiffres</h2>
        </div>

        <div class="stats">
            <?php for ($i = 1; $i <= 3; $i++): ?>
            <div class="stat" data-reveal>
                <b><?= e(get_content("stat_{$i}_value")) ?></b>
                <span><?= e(get_content("stat_{$i}_label")) ?></span>
            </div>
            <?php endfor; ?>
        </div>

        <p class="lead" style="margin-block-start:var(--sp-5); max-inline-size:70ch" data-reveal>
            <?= e(get_content('club_intro')) ?>
        </p>
    </div>
</section>

<!-- =============================================================
     FORMATIONS — filtrables, détail en dialogue natif
     ============================================================= -->
<section class="section section-tint" id="formations" data-hud-label="Formations">
    <div class="wrap">
        <div class="section-head" data-reveal>
            <p class="eyebrow">Formations</p>
            <h2>Du premier baptême à l'encadrement</h2>
            <p class="lead">Classées par profondeur croissante. Filtrez selon votre
               situation, puis ouvrez une carte pour le détail.</p>
        </div>

        <div class="filters" id="formation-filters" role="group" aria-label="Filtrer les formations">
            <span class="filters-label" aria-hidden="true">Filtrer</span>
            <?php foreach ($filters as $key => $label): ?>
                <button type="button" class="chip" data-filter="<?= e($key) ?>"
                        aria-pressed="<?= $key === 'all' ? 'true' : 'false' ?>">
                    <?= e($label) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="formations" id="formations-grid">
            <?php foreach ($formations as $formation): ?>
            <article class="formation" data-tilt data-reveal
                     data-tags="<?= e(implode(' ', formation_tags($formation))) ?>"
                     data-title="<?= e($formation['title']) ?>"
                     data-depth="<?= e($formation['depth_label']) ?>"
                     data-details="<?= e($formation['details']) ?>">
                <div class="formation-head">
                    <span class="formation-icon"><?php render_icon($formation['icon']); ?></span>
                    <span class="depth-badge"><?= e($formation['depth_label']) ?></span>
                </div>
                <h3>
                    <!-- Le bouton porte le titre : il devient le nom accessible de
                         la carte, et sa zone cliquable couvre toute la carte. -->
                    <button type="button" class="formation-open">
                        <?= e($formation['title']) ?>
                    </button>
                </h3>
                <p><?= e($formation['summary']) ?></p>
                <span class="card-cta" aria-hidden="true">Voir le détail <?php render_icon('arrow-right'); ?></span>
            </article>
            <?php endforeach; ?>

            <p class="empty-filter" id="formations-empty" hidden>
                Aucune formation ne correspond à ce filtre pour le moment.
            </p>
        </div>
    </div>
</section>

<!-- =============================================================
     TARIFS
     ============================================================= -->
<section class="section" id="tarifs" data-hud-label="Tarifs">
    <div class="wrap">
        <div class="section-head" data-reveal>
            <p class="eyebrow">Inscriptions &amp; tarifs</p>
            <h2>Un seul montant, tout compris</h2>
            <p class="lead">Chaque ligne regroupe l'adhésion au club, la licence
               FFESSM et, si besoin, la formation. Pas de supplément caché.</p>
        </div>

        <div class="price-table-wrap" data-reveal>
            <table class="price-table">
                <caption class="visually-hidden">Tarifs d'adhésion au club pour la saison</caption>
                <thead>
                    <tr>
                        <th scope="col">Profil</th>
                        <th scope="col">Détail (adhésion + licence + formation)</th>
                        <th scope="col" style="text-align:end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pricing as $row): ?>
                    <tr>
                        <td class="price-label"><?= e($row['label']) ?></td>
                        <td class="price-detail"><?= e($row['detail']) ?></td>
                        <td class="price-total"><?= e($row['price']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="note" data-reveal><?= e(get_content('pricing_note')) ?></p>

        <div class="cta-band" style="margin-block-start:var(--sp-6)" data-reveal>
            <h2>Prêt à vous inscrire&nbsp;?</h2>
            <p>Trois documents à réunir, et c'est réglé. La page d'inscription
               liste les pièces à fournir et met les PDF à télécharger.</p>
            <div class="cta-actions">
                <a href="inscription.php" class="btn btn-lg">
                    <?php render_icon('check'); ?> Voir les étapes d'inscription
                </a>
                <a href="#contact" class="btn btn-lg btn-outline">Poser une question</a>
            </div>
        </div>
    </div>
</section>

<!-- =============================================================
     CONTACT & INFOS PRATIQUES
     ============================================================= -->
<section class="section section-tint" id="contact" data-hud-label="Contact">
    <div class="wrap">
        <div class="section-head" data-reveal>
            <p class="eyebrow">Contact</p>
            <h2>Venez nous voir au bord du bassin</h2>
            <p class="lead">Le plus simple reste de passer nous rencontrer pendant
               un créneau d'entraînement, ou de nous écrire.</p>
        </div>

        <div class="contact-grid">
            <div class="info-list">
                <div class="info-item" data-reveal>
                    <?php render_icon('pin'); ?>
                    <div>
                        <h3>Adresse</h3>
                        <p><?= e(get_content('contact_address')) ?></p>
                    </div>
                </div>
                <div class="info-item" data-reveal>
                    <?php render_icon('clock'); ?>
                    <div>
                        <h3>Créneaux</h3>
                        <p><?= e(get_content('contact_hours', 'Consultez le calendrier de la saison pour les horaires à jour.')) ?></p>
                    </div>
                </div>
                <div class="info-item" data-reveal>
                    <?php render_icon('mail'); ?>
                    <div>
                        <h3>Email</h3>
                        <p><a href="mailto:<?= e(get_content('contact_email')) ?>"><?= e(get_content('contact_email')) ?></a></p>
                    </div>
                </div>
                <div class="info-item" data-reveal>
                    <?php render_icon('facebook'); ?>
                    <div>
                        <h3>Facebook</h3>
                        <p><a href="<?= e(get_content('facebook_url')) ?>" target="_blank" rel="noopener">Plongée Carpentras</a></p>
                    </div>
                </div>
            </div>

            <div class="map-card" data-reveal>
                <?php render_icon('sonar', 'icon icon-xl'); ?>
                <h3>Nous trouver</h3>
                <p><?= e(get_content('contact_address')) ?></p>
                <a class="btn"
                   href="https://www.google.com/maps/search/?api=1&amp;query=<?= urlencode(get_content('contact_address')) ?>"
                   target="_blank" rel="noopener">
                    Ouvrir dans Google&nbsp;Maps <?php render_icon('external'); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Détail d'une formation : dialogue natif, fermeture au clavier incluse -->
<dialog id="formation-dialog" aria-labelledby="fd-title">
    <button type="button" class="dialog-close" data-close aria-label="Fermer"><?php render_icon('close'); ?></button>
    <div class="dialog-body">
        <div class="dialog-head">
            <span class="formation-icon" id="fd-icon" aria-hidden="true"></span>
            <span class="depth-badge" id="fd-depth"></span>
        </div>
        <h2 id="fd-title"></h2>
        <p id="fd-text"></p>
        <div class="dialog-actions">
            <a href="#contact" class="btn" data-close>Demander des informations</a>
            <a href="inscription.php" class="btn btn-outline" data-close>Comment s'inscrire</a>
        </div>
    </div>
</dialog>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
