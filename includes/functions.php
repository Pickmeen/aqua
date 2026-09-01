<?php
/**
 * Fonctions d'accès aux données et utilitaires partagés par tout le site.
 */

require_once __DIR__ . '/../config/db.php';

/** Échappe une chaîne pour un affichage HTML sûr. */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/** Récupère un bloc de contenu éditable depuis site_content. */
function get_content(string $key, string $default = ''): string
{
    static $cache = null;

    if ($cache === null) {
        $cache = [];
        $stmt = get_pdo()->query('SELECT content_key, content_value FROM site_content');
        foreach ($stmt->fetchAll() as $row) {
            $cache[$row['content_key']] = $row['content_value'];
        }
    }

    return $cache[$key] ?? $default;
}

/** Événements du calendrier, triés par date. Filtre optionnel "à venir uniquement". */
function get_events(bool $upcoming_only = false): array
{
    $sql = 'SELECT id, title, event_date, event_time, description FROM events';
    if ($upcoming_only) {
        $sql .= ' WHERE event_date >= CURDATE()';
    }
    $sql .= ' ORDER BY event_date ASC, event_time ASC';

    return get_pdo()->query($sql)->fetchAll();
}

/** Le prochain événement à venir, ou null s'il n'y en a pas. */
function get_next_event(): ?array
{
    $events = get_events(true);
    return $events[0] ?? null;
}

/** Formations proposées par le club, dans l'ordre défini par l'admin (profondeur croissante). */
function get_formations(): array
{
    return get_pdo()
        ->query('SELECT id, title, summary, details, icon, depth_label FROM formations ORDER BY sort_order ASC, id ASC')
        ->fetchAll();
}

/** Grille tarifaire, dans l'ordre défini par l'admin. */
function get_pricing(): array
{
    return get_pdo()
        ->query('SELECT id, label, detail, price FROM pricing ORDER BY sort_order ASC, id ASC')
        ->fetchAll();
}

/** Documents téléchargeables (fiche d'adhésion, CACI...), indexés par doc_key. */
function get_documents(): array
{
    $rows = get_pdo()
        ->query('SELECT doc_key, title, description, filename, original_name FROM documents ORDER BY sort_order ASC, id ASC')
        ->fetchAll();

    $byKey = [];
    foreach ($rows as $row) {
        $byKey[$row['doc_key']] = $row;
    }
    return $byKey;
}

/** Formate une date SQL (YYYY-MM-DD) en français, ex. "samedi 14 mars 2026". */
function format_event_date(string $sql_date): string
{
    $jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
    $mois = ['', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];

    $date = DateTime::createFromFormat('Y-m-d', $sql_date);
    if (!$date) {
        return $sql_date;
    }

    $jour = $jours[(int) $date->format('N') - 1];
    $numero = (int) $date->format('j');
    $moisNom = $mois[(int) $date->format('n')];
    $annee = $date->format('Y');

    return ucfirst($jour) . ' ' . $numero . ' ' . $moisNom . ' ' . $annee;
}

/** Abréviation du mois en français, ex. "MAR" pour mars. Utilisé pour les badges de date. */
function format_month_abbrev(string $sql_date): string
{
    $mois = ['JAN', 'FÉV', 'MAR', 'AVR', 'MAI', 'JUIN', 'JUIL', 'AOÛT', 'SEP', 'OCT', 'NOV', 'DÉC'];
    $date = DateTime::createFromFormat('Y-m-d', $sql_date);
    return $date ? $mois[(int) $date->format('n') - 1] : '';
}

/**
 * Étiquettes de filtrage déduites d'une formation (niveau, milieu, rôle).
 * Elles alimentent les filtres de la section Formations côté public.
 * Déduites du contenu plutôt que stockées : le backoffice reste simple,
 * l'admin n'a pas de cases supplémentaires à cocher.
 */
function formation_tags(array $formation): array
{
    $tags = [];
    $depth = trim($formation['depth_label'] ?? '');
    $title = mb_strtolower($formation['title'] ?? '', 'UTF-8');
    $icon  = $formation['icon'] ?? '';

    $is_pool = mb_stripos($depth, 'piscine') !== false;
    $tags[] = $is_pool ? 'piscine' : 'mer';

    $beginner_titles = ['baptême', 'bapteme', 'enfant', 'niveau 1', 'découverte', 'decouverte'];
    foreach ($beginner_titles as $needle) {
        if (mb_strpos($title, $needle) !== false) { $tags[] = 'debutant'; break; }
    }
    if ($is_pool && !in_array('debutant', $tags, true)) {
        $tags[] = 'debutant';
    }

    $supervising = in_array($icon, ['whistle', 'anchor'], true)
        || mb_strpos($title, 'initiateur') !== false
        || mb_strpos($title, 'niveau 4') !== false
        || mb_strpos($title, 'moniteur') !== false;
    if ($supervising) {
        $tags[] = 'encadrement';
        $tags = array_values(array_diff($tags, ['debutant']));
    }

    return array_values(array_unique($tags));
}

/** Nombre de jours (entier, signé) entre aujourd'hui et une date SQL. */
function days_until(string $sql_date): ?int
{
    $target = DateTime::createFromFormat('Y-m-d', $sql_date);
    if (!$target) {
        return null;
    }
    $target->setTime(0, 0);
    $today = new DateTime('today');

    return (int) $today->diff($target)->format('%r%a');
}

/** Formulation lisible d'une échéance : « demain », « dans 12 jours »... */
function countdown_label(string $sql_date): string
{
    $days = days_until($sql_date);
    if ($days === null)  { return ''; }
    if ($days < 0)       { return 'passé'; }
    if ($days === 0)     { return "aujourd'hui"; }
    if ($days === 1)     { return 'demain'; }

    return 'dans ' . $days . ' jours';
}

/**
 * Vrai si cette copie du site est une préproduction.
 *
 * Repérée par la présence d'un fichier vide `preprod.flag` à la racine.
 * Ce fichier n'est jamais versionné (.gitignore) : impossible de l'envoyer
 * en production par mégarde. Sur une préprod, le site se marque noindex,
 * n'envoie rien à Google Analytics et affiche un bandeau d'avertissement.
 */
function is_preprod(): bool
{
    static $flag = null;

    if ($flag === null) {
        $flag = file_exists(dirname(__DIR__) . '/preprod.flag');
    }

    return $flag;
}
