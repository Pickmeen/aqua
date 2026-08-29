<?php
$page_title = 'Tableau de bord';
$active_nav = 'dashboard';
require_once __DIR__ . '/includes/admin_header.php';

$nbUpcoming = (int) get_pdo()->query('SELECT COUNT(*) AS c FROM events WHERE event_date >= CURDATE()')->fetch()['c'];
$nbFormations = (int) get_pdo()->query('SELECT COUNT(*) AS c FROM formations')->fetch()['c'];
$nbPricing = (int) get_pdo()->query('SELECT COUNT(*) AS c FROM pricing')->fetch()['c'];
?>

<p style="color:var(--ink-500); margin-bottom:1.8rem;">
    Bonjour <?= e($_SESSION['admin_username'] ?? '') ?>, voici un aperçu du contenu du site.
</p>

<div class="admin-stats">
    <div class="admin-stat-card">
        <strong><?= $nbUpcoming ?></strong>
        <span>Événement(s) à venir</span>
        <a href="events.php">Gérer le calendrier →</a>
    </div>
    <div class="admin-stat-card">
        <strong><?= $nbFormations ?></strong>
        <span>Formation(s) publiée(s)</span>
        <a href="formations.php">Gérer les formations →</a>
    </div>
    <div class="admin-stat-card">
        <strong><?= $nbPricing ?></strong>
        <span>Ligne(s) tarifaires</span>
        <a href="pricing.php">Gérer les tarifs →</a>
    </div>
</div>

<div class="admin-form" style="max-width:none;">
    <h2 style="margin-bottom:.8rem;">Accès rapides</h2>
    <p style="color:var(--ink-500); margin-bottom:1.2rem;">
        Modifie les coordonnées du club, le texte d'accueil ou les mentions légales depuis
        <a href="content.php">Contenu du site</a>. Le site public reflète tes changements immédiatement après enregistrement.
    </p>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
