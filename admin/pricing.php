<?php
$page_title = 'Tarifs';
$active_nav = 'pricing';
require_once __DIR__ . '/includes/admin_header.php';

$pricing = get_pdo()->query('SELECT id, label, detail, price, sort_order FROM pricing ORDER BY sort_order ASC, id ASC')->fetchAll();
?>

<div class="admin-toolbar">
    <p style="color:var(--ink-500);"><?= count($pricing) ?> ligne(s) tarifaire(s).</p>
    <a href="pricing_form.php" class="btn btn-primary btn-sm">
        <?php render_icon('plus', 'icon'); ?> Ajouter une ligne
    </a>
</div>

<div class="admin-table-wrap">
    <?php if (empty($pricing)): ?>
        <p class="admin-empty">Aucune ligne tarifaire pour le moment.</p>
    <?php else: ?>
    <table class="admin-table">
        <thead>
            <tr><th>Ordre</th><th>Adhésion</th><th>Détail</th><th>Prix</th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach ($pricing as $p): ?>
            <tr>
                <td><?= (int) $p['sort_order'] ?></td>
                <td><?= e($p['label']) ?></td>
                <td style="color:var(--ink-500);"><?= e($p['detail']) ?></td>
                <td><strong style="color:var(--coral-500);"><?= e($p['price']) ?></strong></td>
                <td>
                    <div class="admin-row-actions">
                        <a class="icon-btn" href="pricing_form.php?id=<?= (int) $p['id'] ?>" aria-label="Modifier"><?php render_icon('edit', 'icon'); ?></a>
                        <form method="post" action="pricing_delete.php" onsubmit="return confirm('Supprimer cette ligne tarifaire ?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
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
