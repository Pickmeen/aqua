<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Calendrier — ' . get_content('site_name', 'Plongée Carpentras');
$page_description = 'Calendrier de la saison du Club Subaquatique du Comtat Venaissin : sorties, réunions et baptêmes de plongée à Carpentras.';
require_once __DIR__ . '/includes/header.php';

$events = get_events();
$upcoming = get_events(true);

$events_json = array_map(static function (array $ev): array {
    return [
        'title' => $ev['title'],
        'date' => $ev['event_date'],
        'time' => $ev['event_time'],
        'description' => $ev['description'],
    ];
}, $events);
?>

<section class="page-hero-sm">
    <div class="container">
        <p class="eyebrow">Le club</p>
        <h1>Calendrier de la saison</h1>
        <p class="section-lead">Sorties, réunions, baptêmes et rendez-vous du club, mis à jour régulièrement.</p>
    </div>
</section>

<div class="page-body">
    <section class="section">
        <div class="container calendar-layout">
            <div class="calendar-card" data-reveal>
                <div class="calendar-header">
                    <button id="prevMonth" class="calendar-nav" aria-label="Mois précédent"><?php render_icon('chevron-left', 'icon'); ?></button>
                    <span id="monthYear"></span>
                    <button id="nextMonth" class="calendar-nav" aria-label="Mois suivant"><?php render_icon('chevron-right', 'icon'); ?></button>
                </div>
                <table class="calendar-table" id="calendar">
                    <thead>
                        <tr><th>Lun</th><th>Mar</th><th>Mer</th><th>Jeu</th><th>Ven</th><th>Sam</th><th>Dim</th></tr>
                    </thead>
                    <tbody id="calendarBody"></tbody>
                </table>
            </div>

            <div class="events-list">
                <h2 data-reveal>Événements à venir</h2>
                <?php if (empty($upcoming)): ?>
                    <p class="empty-state" data-reveal>Aucun événement programmé pour le moment. Revenez bientôt !</p>
                <?php else: ?>
                    <?php foreach ($upcoming as $ev): ?>
                    <article class="event-card" data-reveal>
                        <div class="event-date-badge">
                            <span class="event-day"><?= e(date('d', strtotime($ev['event_date']))) ?></span>
                            <span class="event-month"><?= e(format_month_abbrev($ev['event_date'])) ?></span>
                        </div>
                        <div class="event-body">
                            <h3><?= e($ev['title']) ?></h3>
                            <p class="event-meta"><?= e(format_event_date($ev['event_date'])) ?><?= $ev['event_time'] ? ' &middot; ' . e(substr($ev['event_time'], 0, 5)) : '' ?></p>
                            <?php if (!empty($ev['description'])): ?>
                                <p><?= e($ev['description']) ?></p>
                            <?php endif; ?>
                        </div>
                    </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<!-- POPUP EVENEMENT (clic sur une date du calendrier) -->
<div id="eventModal" class="popup" role="dialog" aria-modal="true">
    <div class="popup-content">
        <button class="close-popup" aria-label="Fermer">&times;</button>
        <h2 id="modalEventTitle"></h2>
        <p id="modalEventDate" class="event-meta"></p>
        <p id="modalEventDetails"></p>
    </div>
</div>

<script>
    window.CSCV_EVENTS = <?= json_encode($events_json, JSON_UNESCAPED_UNICODE) ?>;
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
