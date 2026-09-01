<?php
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Nous rejoindre — ' . get_content('site_name', 'Plongée Carpentras');
$page_description = 'Comment rejoindre le Club Subaquatique du Comtat Venaissin à Carpentras : fiche d’adhésion, certificat médical (CACI) et autorisation parentale.';

require_once __DIR__ . '/includes/header.php';

$documents = get_documents();

$steps = [
    [
        'title'       => 'Remplir la fiche d’adhésion',
        'description' => 'Téléchargez la fiche, complétez-la, et apportez-la le jour de votre inscription. Elle sert aussi à la demande de licence FFESSM.',
        'doc_key'     => 'fiche_adhesion',
    ],
    [
        'title'       => 'Fournir un certificat médical (CACI)',
        'description' => 'Le Certificat d’Absence de Contre-Indication à la plongée est obligatoire. Il est établi par votre médecin traitant, et daté de moins d’un an.',
        'doc_key'     => 'caci',
    ],
    [
        'title'       => 'Autorisation parentale (mineurs)',
        'description' => 'Pour les adhérents de moins de 18 ans, l’autorisation doit être signée par un responsable légal.',
        'doc_key'     => 'autorisation_parentale',
    ],
];

$faq = [
    'Faut-il savoir bien nager ?' => 'Il faut être à l’aise dans l’eau et savoir nager, sans niveau de compétition. Un test simple est réalisé en début de saison.',
    'Faut-il acheter du matériel ?' => 'Non. Le club prête l’équipement lourd (bloc, détendeur, stab). Masque, palmes et tuba personnels sont recommandés à terme, mais pas obligatoires pour commencer.',
    'À partir de quel âge ?' => 'Dès 10 ans pour la plongée enfants, en piscine. Les formations fédérales de Niveau 3 et 4 sont accessibles à partir de 18 ans.',
    'Peut-on essayer avant de s’inscrire ?' => 'Oui, c’est même conseillé : le baptême de plongée est ouvert à tous, sans adhésion préalable. Contactez le club pour convenir d’une date.',
];
?>

<section class="page-hero">
    <div class="wrap">
        <nav class="breadcrumb" aria-label="Fil d'Ariane">
            <a href="index.php">Accueil</a>
            <?php render_icon('chevron-right'); ?>
            <span>Nous rejoindre</span>
        </nav>
        <p class="eyebrow">Adhésion</p>
        <h1>Rejoindre le club en trois étapes</h1>
        <p class="lead">Réunissez ces trois pièces, présentez-vous à un créneau
           d'entraînement, et vous êtes adhérent pour la saison.</p>
    </div>
</section>

<section class="section">
    <div class="wrap-narrow">
        <div class="steps">
            <?php foreach ($steps as $i => $step): ?>
            <?php $doc = $documents[$step['doc_key']] ?? null; ?>
            <article class="step" data-reveal>
                <div class="step-num" aria-hidden="true"><?= $i + 1 ?></div>
                <div class="step-body">
                    <h3><?= e($step['title']) ?></h3>
                    <p><?= e($step['description']) ?></p>

                    <?php if ($doc && !empty($doc['filename'])): ?>
                        <a class="doc-link" href="uploads/documents/<?= e($doc['filename']) ?>"
                           target="_blank" rel="noopener" download>
                            <?php render_icon('download'); ?>
                            Télécharger le PDF
                        </a>
                    <?php else: ?>
                        <span class="doc-missing">
                            <?php render_icon('mail'); ?>
                            Document bientôt disponible — écrivez-nous en attendant
                        </span>
                    <?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <div class="cta-band" style="margin-block-start:var(--sp-6)" data-reveal>
            <h2>Combien ça coûte&nbsp;?</h2>
            <p>Les tarifs regroupent l'adhésion au club, la licence FFESSM et,
               selon le profil, la formation. Tout est détaillé sur une seule page.</p>
            <div class="cta-actions">
                <a href="index.php#tarifs" class="btn"><?php render_icon('euro'); ?> Voir les tarifs</a>
                <a href="index.php#contact" class="btn btn-outline">Poser une question</a>
            </div>
        </div>
    </div>
</section>

<section class="section section-tint">
    <div class="wrap-narrow">
        <div class="section-head" data-reveal>
            <p class="eyebrow">Questions fréquentes</p>
            <h2>Ce qu'on nous demande le plus souvent</h2>
        </div>

        <div class="steps">
            <?php foreach ($faq as $question => $answer): ?>
            <article class="step" data-reveal style="grid-template-columns:auto minmax(0,1fr)">
                <div class="step-num" aria-hidden="true" style="background:var(--c-brand-soft);color:var(--c-brand-ink)">?</div>
                <div class="step-body">
                    <h3><?= e($question) ?></h3>
                    <p><?= e($answer) ?></p>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
