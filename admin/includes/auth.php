<?php
/**
 * Authentification et protections du backoffice (session, CSRF, throttling).
 */

require_once __DIR__ . '/../../includes/functions.php';

/*
 * Préproduction en lecture seule : la copie de test partage la base de
 * production, le backoffice est donc verrouillé pour qu'aucune
 * manipulation d'essai ne modifie le vrai site.
 *
 * Le verrou est placé ici parce que toutes les pages qui écrivent en base
 * incluent ce fichier en premier — y compris setup.php. Le site public,
 * qui ne fait que des SELECT, reste entièrement consultable.
 */
if (preprod_is_readonly()) {
    http_response_code(403);
    header('Content-Type: text/html; charset=UTF-8');
    header('X-Robots-Tag: noindex, nofollow');
    ?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Backoffice verrouillé — préproduction</title>
<style>
    body { margin: 0; display: grid; place-items: center; min-height: 100vh;
           font-family: system-ui, -apple-system, 'Segoe UI', sans-serif;
           background: #f4f8fa; color: #08202c; padding: 1.5rem; }
    main { max-width: 34rem; background: #fff; border: 1px solid #d7e4ea;
           border-radius: 20px; padding: 2rem; box-shadow: 0 10px 30px rgb(8 32 44 / .08); }
    h1 { font-size: 1.35rem; margin: 0 0 .9rem; }
    p { line-height: 1.6; color: #4c6675; margin: 0 0 .9rem; }
    code { background: #f0f6f9; padding: .15rem .4rem; border-radius: 5px; font-size: .9em; }
    a { color: #0d7b8e; }
</style>
</head>
<body>
<main>
    <h1>Backoffice verrouillé sur cette préproduction</h1>
    <p>Cette copie de test partage la base de données du site en ligne.
       Le backoffice est donc bloqué : toute modification enregistrée ici
       s'appliquerait au vrai site.</p>
    <p>Vous pouvez parcourir librement <a href="../index.php">le site public</a>
       de cette préproduction, avec le contenu réel.</p>
    <p>Pour déverrouiller le backoffice, il faut donner à la préproduction
       sa propre base de données, puis retirer le mot <code>readonly</code>
       du fichier <code>preprod.flag</code>.</p>
</main>
</body>
</html><?php
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    ]);
    session_start();
}

define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCK_SECONDS', 60);

function is_logged_in(): bool
{
    return !empty($_SESSION['admin_id']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

/** Jeton CSRF pour les formulaires admin. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Session expirée ou requête invalide. Merci de recharger la page et réessayer.');
    }
}

/** Petite protection anti brute-force sur le formulaire de connexion. */
function login_attempts_exceeded(): bool
{
    $data = $_SESSION['login_throttle'] ?? ['count' => 0, 'locked_until' => 0];
    return $data['locked_until'] > time();
}

function register_failed_login(): void
{
    $data = $_SESSION['login_throttle'] ?? ['count' => 0, 'locked_until' => 0];
    $data['count'] = ($data['count'] ?? 0) + 1;
    if ($data['count'] >= LOGIN_MAX_ATTEMPTS) {
        $data['locked_until'] = time() + LOGIN_LOCK_SECONDS;
        $data['count'] = 0;
    }
    $_SESSION['login_throttle'] = $data;
}

function reset_login_attempts(): void
{
    unset($_SESSION['login_throttle']);
}

/** Message flash (succès/erreur) affiché une seule fois après redirection. */
function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}
