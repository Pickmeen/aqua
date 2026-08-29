<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Inscription — ' . get_content('site_name', 'Plongée Carpentras');
$page_description = 'Comment rejoindre le Club Subaquatique du Comtat Venaissin à Carpentras : fiche d’adhésion, certificat médical (CACI) et autorisation parentale.';
require_once __DIR__ . '/includes/header.php';

$documents = get_documents();

$steps = [
    [
        'title' => 'Remplir la fiche d’adhésion',
        'description' => 'Téléchargez et complétez la fiche d’adhésion du club, à apporter le jour de votre inscription.',
        'doc_key' => 'fiche_adhesion',
    ],
    [
        'title' => 'Fournir un certificat médical (CACI)',
        'description' => 'Le Certificat d’Absence de Contre-indication à la plongée est obligatoire, à faire établir par votre médecin.',
        'doc_key' => 'caci',
    ],
    [
        'title' => 'Autorisation parentale (mineurs)',
        'description' => 'Pour les adhérents mineurs, l’autorisation doit être signée par un responsable légal.',
        'doc_key' => 'autorisation_parentale',
    ],
];
?>

<section class="page-hero-sm">
    <div class="container">
        <p class="eyebrow">Rejoindre le club</p>
        <h1>Étapes d'inscription</h1>
        <p class="section-lead">Trois étapes simples pour devenir adhérent du Club Subaquatique du Comtat Venaissin.</p>
    </div>
</section>

<div class="page-body">
    <section class="section">
        <div class="container">
            <div class="steps-list">
                <?php foreach ($steps as $i => $step): ?>
                <?php $doc = $documents[$step['doc_key']] ?? null; ?>
                <div class="step-card" data-reveal>
                    <div class="step-number"><?= $i + 1 ?></div>
                    <div class="step-body">
                        <h3><?= e($step['title']) ?></h3>
                        <p><?= e($step['description']) ?></p>
                        <?php if ($doc && !empty($doc['filename'])): ?>
                            <a class="doc-download" href="uploads/documents/<?= e($doc['filename']) ?>" target="_blank" rel="noopener" download>
                                <?php render_icon('arrow-down', 'icon'); ?>
                                Télécharger le PDF
                            </a>
                        <?php else: ?>
                            <span class="doc-unavailable">
                                <?php render_icon('mail', 'icon'); ?>
                                Document à venir — contactez le club en attendant
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="center-cta" data-reveal>
                <p style="color: var(--ink-500); margin-bottom: 1.4rem;">Une question sur votre dossier d'inscription&nbsp;?</p>
                <div class="hero-actions" style="justify-content: center;">
                    <a href="index.php#contact" class="btn btn-primary">Contacter le club</a>
                    <a href="index.php#tarifs" class="btn btn-outline">Revoir les tarifs</a>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
