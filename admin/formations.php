<?php
$page_title = 'Formations';
$active_nav = 'formations';
require_once __DIR__ . '/includes/admin_header.php';

$formations = get_pdo()->query('SELECT id, title, summary, icon, depth_label, sort_order FROM formations ORDER BY sort_order ASC, id ASC')->fetchAll();
?>

<div class="admin-toolbar">
    <p style="color:var(--ink-500);"><?= count($formations) ?> formation(s). L'ordre d'affichage suit la colonne « Ordre ».</p>
    <a href="formation_form.php" class="btn btn-primary btn-sm">
        <?php render_icon('plus', 'icon'); ?> Ajouter une formation
    </a>
</div>

<div class="admin-table-wrap">
    <?php if (empty($formations)): ?>
        <p class="admin-empty">Aucune formation pour le moment.</p>
    <?php else: ?>
    <table class="admin-table">
        <thead>
            <tr><th>Ordre</th><th></th><th>Titre</th><th>Profondeur</th><th>Résumé</th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach ($formations as $f): ?>
            <tr>
                <td><?= (int) $f['sort_order'] ?></td>
                <td><?php render_icon($f['icon'], 'icon'); ?></td>
                <td><?= e($f['title']) ?></td>
                <td><span class="depth-badge"><?= e($f['depth_label']) ?></span></td>
                <td style="max-width:320px; color:var(--ink-500);"><?= e(mb_strimwidth($f['summary'], 0, 90, '…')) ?></td>
                <td>
                    <div class="admin-row-actions">
                        <a class="icon-btn" href="formation_form.php?id=<?= (int) $f['id'] ?>" aria-label="Modifier"><?php render_icon('edit', 'icon'); ?></a>
                        <form method="post" action="formation_delete.php" onsubmit="return confirm('Supprimer cette formation ?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= (int) $f['id'] ?>">
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
