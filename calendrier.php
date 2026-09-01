<?php
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Calendrier de la saison — ' . get_content('site_name', 'Plongée Carpentras');
$page_description = 'Calendrier de la saison du Club Subaquatique du Comtat Venaissin : sorties, réunions et baptêmes de plongée à Carpentras.';

require_once __DIR__ . '/includes/header.php';

$events   = get_events();
$upcoming = get_events(true);
$today    = date('Y-m-d');
$past     = array_reverse(array_filter($events, static fn(array $ev): bool => $ev['event_date'] < $today));

// Le calendrier mensuel est rendu côté client à partir de cette liste.
$events_json = array_map(static fn(array $ev): array => [
    'title'       => $ev['title'],
    'date'        => $ev['event_date'],
    'time'        => $ev['event_time'],
    'description' => $ev['description'],
], $events);
?>

<section class="page-hero">
    <div class="wrap">
        <nav class="breadcrumb" aria-label="Fil d'Ariane">
            <a href="index.php">Accueil</a>
            <?php render_icon('chevron-right'); ?>
            <span>Calendrier</span>
        </nav>
        <p class="eyebrow">Saison en cours</p>
        <h1>Calendrier du club</h1>
        <p class="lead">Sorties, réunions, baptêmes et rendez-vous, mis à jour par
           le bureau dès qu'une date est confirmée.</p>
    </div>
</section>

<section class="section">
    <div class="wrap calendar-layout">
        <div class="calendar-card" data-reveal>
            <div class="calendar-head">
                <button type="button" class="icon-btn" id="cal-prev" aria-label="Mois précédent">
                    <?php render_icon('chevron-left'); ?>
                </button>
                <b id="calendar-label" aria-live="polite">—</b>
                <button type="button" class="icon-btn" id="cal-next" aria-label="Mois suivant">
                    <?php render_icon('chevron-right'); ?>
                </button>
            </div>

            <table class="calendar-table">
                <caption class="visually-hidden">Calendrier mensuel des événements du club</caption>
                <thead>
                    <tr>
                        <th scope="col"><abbr title="lundi">Lun</abbr></th>
                        <th scope="col"><abbr title="mardi">Mar</abbr></th>
                        <th scope="col"><abbr title="mercredi">Mer</abbr></th>
                        <th scope="col"><abbr title="jeudi">Jeu</abbr></th>
                        <th scope="col"><abbr title="vendredi">Ven</abbr></th>
                        <th scope="col"><abbr title="samedi">Sam</abbr></th>
                        <th scope="col"><abbr title="dimanche">Dim</abbr></th>
                    </tr>
                </thead>
                <tbody id="calendar-body"></tbody>
            </table>

            <p class="calendar-legend">
                <span><i class="legend-dot" aria-hidden="true"></i> Événement (cliquez pour le détail)</span>
                <span><i class="legend-dot today" aria-hidden="true"></i> Aujourd'hui</span>
            </p>
        </div>

        <div>
            <h2 style="margin-block-end:var(--sp-4)" data-reveal>À venir</h2>

            <?php if (empty($upcoming)): ?>
                <p class="empty-state" data-reveal>
                    Aucune date programmée pour l'instant. Les prochains rendez-vous
                    seront publiés ici et sur la page Facebook du club.
                </p>
            <?php else: ?>
                <div class="events">
                    <?php foreach ($upcoming as $ev): ?>
                    <article class="event" data-reveal>
                        <div class="event-date" aria-hidden="true">
                            <b><?= e(date('d', strtotime($ev['event_date']))) ?></b>
                            <span><?= e(format_month_abbrev($ev['event_date'])) ?></span>
                        </div>
                        <div>
                            <h3><?= e($ev['title']) ?></h3>
                            <p class="event-meta">
                                <span><?= e(format_event_date($ev['event_date'])) ?><?= $ev['event_time'] ? ' · ' . e(substr($ev['event_time'], 0, 5)) : '' ?></span>
                                <span class="countdown" data-countdown="<?= e($ev['event_date']) ?>"><?= e(countdown_label($ev['event_date'])) ?></span>
                            </p>
                            <?php if (!empty($ev['description'])): ?>
                                <p><?= e($ev['description']) ?></p>
                            <?php endif; ?>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($past)): ?>
            <details style="margin-block-start:var(--sp-5)">
                <summary class="link-arrow" style="cursor:pointer">
                    Voir les <?= count($past) ?> rendez-vous passés
                </summary>
                <div class="events" style="margin-block-start:var(--sp-3)">
                    <?php foreach (array_slice($past, 0, 12) as $ev): ?>
                    <article class="event is-past">
                        <div class="event-date" aria-hidden="true">
                            <b><?= e(date('d', strtotime($ev['event_date']))) ?></b>
                            <span><?= e(format_month_abbrev($ev['event_date'])) ?></span>
                        </div>
                        <div>
                            <h3><?= e($ev['title']) ?></h3>
                            <p class="event-meta"><?= e(format_event_date($ev['event_date'])) ?></p>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </details>
            <?php endif; ?>

            <div class="cta-band" style="margin-block-start:var(--sp-6)" data-reveal>
                <h2>Envie de participer&nbsp;?</h2>
                <p>Les sorties sont ouvertes aux adhérents ; les baptêmes, à tout le monde.</p>
                <div class="cta-actions">
                    <a href="index.php#contact" class="btn">Nous contacter</a>
                    <a href="inscription.php" class="btn btn-outline">S'inscrire au club</a>
                </div>
            </div>
        </div>
    </div>
</section>

<dialog id="event-dialog" aria-labelledby="ev-title">
    <button type="button" class="dialog-close" data-close aria-label="Fermer"><?php render_icon('close'); ?></button>
    <div class="dialog-body">
        <h2 id="ev-title"></h2>
        <p class="event-meta" id="ev-meta"></p>
        <p id="ev-text"></p>
    </div>
</dialog>

<script>
    window.CSCV_EVENTS = <?= json_encode($events_json, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
