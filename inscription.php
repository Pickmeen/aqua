<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Inscriptions & tarifs — ' . get_content('site_name', 'Plongée Carpentras');
require_once __DIR__ . '/includes/header.php';

$pricing = get_pricing();
?>

<section class="page-hero page-hero-sm">
    <div class="container">
        <p class="eyebrow">Rejoindre le club</p>
        <h1>Inscriptions &amp; tarifs</h1>
        <p class="section-lead">Adhésion annuelle, licence FFESSM et formation associées selon votre profil.</p>
    </div>
</section>

<section class="section" data-reveal>
    <div class="container">
        <div class="price-table-wrap">
            <table class="price-table">
                <thead>
                    <tr>
                        <th>Adhésion</th>
                        <th>Licence + cotisations + formation</th>
                        <th>Prix total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pricing as $row): ?>
                    <tr>
                        <td data-label="Adhésion"><?= e($row['label']) ?></td>
                        <td data-label="Détail"><?= e($row['detail']) ?></td>
                        <td data-label="Prix total" class="total-price"><?= e($row['price']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="note"><?= e(get_content('pricing_note')) ?></p>

        <div class="center-cta">
            <a href="contact.php" class="btn btn-primary">Une question sur les tarifs&nbsp;?</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
