<?php
$page_title = 'Calendrier';
$active_nav = 'events';
require_once __DIR__ . '/includes/admin_header.php';

$events = get_pdo()->query('SELECT id, title, event_date, event_time, description FROM events ORDER BY event_date DESC')->fetchAll();
?>

<div class="admin-toolbar">
    <p style="color:var(--ink-500);"><?= count($events) ?> événement(s) au total.</p>
    <a href="event_form.php" class="btn btn-primary btn-sm">
        <?php render_icon('plus', 'icon'); ?> Ajouter un événement
    </a>
</div>

<div class="admin-table-wrap">
    <?php if (empty($events)): ?>
        <p class="admin-empty">Aucun événement pour le moment.</p>
    <?php else: ?>
    <table class="admin-table">
        <thead>
            <tr><th>Date</th><th>Titre</th><th>Description</th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach ($events as $ev): ?>
            <tr>
                <td style="white-space:nowrap;">
                    <?= e(format_event_date($ev['event_date'])) ?>
                    <?php if ($ev['event_time']): ?><br><small style="color:var(--ink-500);"><?= e(substr($ev['event_time'], 0, 5)) ?></small><?php endif; ?>
                </td>
                <td><?= e($ev['title']) ?></td>
                <td style="max-width:320px; color:var(--ink-500);"><?= e(mb_strimwidth($ev['description'] ?? '', 0, 90, '…')) ?></td>
                <td>
                    <div class="admin-row-actions">
                        <a class="icon-btn" href="event_form.php?id=<?= (int) $ev['id'] ?>" aria-label="Modifier"><?php render_icon('edit', 'icon'); ?></a>
                        <form method="post" action="event_delete.php" onsubmit="return confirm('Supprimer cet événement ?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= (int) $ev['id'] ?>">
                            <button type="submit" class="icon-btn danger" aria-label="Supprimer"><?php render_icon('trash', 'icon'); ?></button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
