<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

const MAX_PDF_SIZE = 10 * 1024 * 1024; // 10 Mo
$upload_dir = __DIR__ . '/../uploads/documents/';

$documents = get_pdo()->query('SELECT * FROM documents ORDER BY sort_order ASC, id ASC')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $docId = (int) ($_POST['doc_id'] ?? 0);

    $current = null;
    foreach ($documents as $d) {
        if ((int) $d['id'] === $docId) { $current = $d; break; }
    }

    if (!$current) {
        set_flash('error', 'Document introuvable.');
        header('Location: documents.php');
        exit;
    }

    if (isset($_POST['remove_file'])) {
        if ($current['filename'] && is_file($upload_dir . $current['filename'])) {
            unlink($upload_dir . $current['filename']);
        }
        $update = get_pdo()->prepare('UPDATE documents SET filename = NULL, original_name = NULL WHERE id = ?');
        $update->execute([$docId]);
        set_flash('success', 'Fichier PDF retiré. Le document reste affiché comme "à venir".');
        header('Location: documents.php');
        exit;
    }

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '') ?: null;
    $errors = [];

    if ($title === '') {
        $errors[] = 'Le titre est obligatoire.';
    }

    $newFilename = $current['filename'];
    $newOriginalName = $current['original_name'];

    if (!empty($_FILES['pdf_file']['name'])) {
        $file = $_FILES['pdf_file'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Échec de l'envoi du fichier.";
        } elseif ($file['size'] > MAX_PDF_SIZE) {
            $errors[] = 'Le fichier dépasse la taille maximale de 10 Mo.';
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if ($mime !== 'application/pdf' || $ext !== 'pdf') {
                $errors[] = 'Seuls les fichiers PDF sont acceptés.';
            } else {
                $storedName = $current['doc_key'] . '-' . time() . '.pdf';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                if (!move_uploaded_file($file['tmp_name'], $upload_dir . $storedName)) {
                    $errors[] = 'Impossible d\'enregistrer le fichier sur le serveur.';
                } else {
                    if ($current['filename'] && is_file($upload_dir . $current['filename'])) {
                        unlink($upload_dir . $current['filename']);
                    }
                    $newFilename = $storedName;
                    $newOriginalName = basename($file['name']);
                }
            }
        }
    }

    if (!$errors) {
        $stmt = get_pdo()->prepare('UPDATE documents SET title=?, description=?, filename=?, original_name=?, uploaded_at = IF(filename <> ? OR filename IS NULL, NOW(), uploaded_at) WHERE id=?');
        $stmt->execute([$title, $description, $newFilename, $newOriginalName, $newFilename, $docId]);
        set_flash('success', 'Document mis à jour.');
    } else {
        set_flash('error', implode(' ', $errors));
    }

    header('Location: documents.php');
    exit;
}

$page_title = 'Documents';
$active_nav = 'documents';
require_once __DIR__ . '/includes/admin_header.php';
?>

<p style="color:var(--ink-500); margin-bottom:1.8rem;">
    Ces documents sont proposés au téléchargement sur la page <strong>Inscription</strong> du site, à chaque étape.
    Seuls les fichiers PDF sont acceptés (10 Mo maximum).
</p>

<?php foreach ($documents as $doc): ?>
<form class="admin-form" method="post" enctype="multipart/form-data" style="max-width:640px; margin-bottom:1.6rem;">
    <?= csrf_field() ?>
    <input type="hidden" name="doc_id" value="<?= (int) $doc['id'] ?>">

    <div class="form-group">
        <label>Titre</label>
        <input class="form-control" type="text" name="title" required value="<?= e($doc['title']) ?>">
    </div>

    <div class="form-group">
        <label>Description <span class="hint">(optionnel)</span></label>
        <input class="form-control" type="text" name="description" value="<?= e($doc['description'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label>Fichier PDF actuel</label>
        <?php if ($doc['filename']): ?>
            <p style="display:flex; align-items:center; gap:1rem; flex-wrap:wrap;">
                <a class="doc-download" style="display:inline-flex;" href="../uploads/documents/<?= e($doc['filename']) ?>" target="_blank" rel="noopener">
                    <?php render_icon('arrow-down', 'icon'); ?> <?= e($doc['original_name'] ?? $doc['filename']) ?>
                </a>
                <button type="submit" name="remove_file" value="1" onclick="return confirm('Retirer ce PDF ? La page Inscription affichera « document à venir ».');" style="background:none; border:none; color:var(--coral-600); font-size:.85rem; font-weight:700; cursor:pointer; padding:0;">Retirer</button>
            </p>
        <?php else: ?>
            <p class="doc-unavailable">Aucun fichier — la page Inscription affiche "document à venir".</p>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="pdf_file-<?= (int) $doc['id'] ?>">Remplacer / ajouter le PDF</label>
        <input class="form-control" type="file" id="pdf_file-<?= (int) $doc['id'] ?>" name="pdf_file" accept="application/pdf">
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>
</form>
<?php endforeach; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
