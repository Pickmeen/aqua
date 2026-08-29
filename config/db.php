<?php
/**
 * Connexion à la base de données MySQL (hébergement OVH mutualisé).
 *
 * Renseigne ces 4 constantes avec les identifiants fournis par OVH
 * (Manager OVH > Hébergements > Bases de données), puis ne les modifie
 * plus. Ce fichier ne doit jamais être accessible publiquement : il est
 * bloqué par .htaccess et ne doit pas contenir de vrais secrets dans un
 * dépôt Git public — utilise plutôt des variables d'environnement si ton
 * hébergement le permet, sinon édite ce fichier uniquement une fois
 * déployé sur le serveur (hors du dépôt versionné).
 */

define('DB_HOST', getenv('DB_HOST') ?: 'A_COMPLETER.mysql.db');
define('DB_NAME', getenv('DB_NAME') ?: 'A_COMPLETER');
define('DB_USER', getenv('DB_USER') ?: 'A_COMPLETER');
define('DB_PASS', getenv('DB_PASS') ?: 'A_COMPLETER');

function get_pdo(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log('Connexion base de données impossible : ' . $e->getMessage());
            http_response_code(500);
            die('Le site est momentanément indisponible. Merci de réessayer plus tard.');
        }
    }

    return $pdo;
}
