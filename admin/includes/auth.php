<?php
/**
 * Authentification et protections du backoffice (session, CSRF, throttling).
 */

require_once __DIR__ . '/../../includes/functions.php';

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
