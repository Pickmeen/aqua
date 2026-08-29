-- =====================================================================
-- Plongée Carpentras - CSCV — Schéma de base de données
-- Compatible MySQL 5.7+ / MariaDB (hébergement mutualisé OVH)
--
-- Installation : importer ce fichier via phpMyAdmin (Manager OVH),
-- puis suivre les instructions du README.md pour créer le premier
-- compte administrateur.
-- =====================================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- ---------------------------------------------------------------------
-- Comptes administrateurs (backoffice)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS admin_users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Contenu éditable du site (textes, coordonnées, réseaux sociaux...)
-- Stocké en paires clé/valeur pour rester simple à administrer.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS site_content (
    content_key   VARCHAR(100) PRIMARY KEY,
    content_value TEXT         NOT NULL,
    updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Événements du calendrier
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS events (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(150) NOT NULL,
    event_date  DATE         NOT NULL,
    event_time  VARCHAR(20)  DEFAULT NULL,
    description TEXT         DEFAULT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_event_date (event_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Formations proposées par le club
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS formations (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(150) NOT NULL,
    summary     VARCHAR(255) NOT NULL,
    details     TEXT         DEFAULT NULL,
    icon        VARCHAR(30)  NOT NULL DEFAULT 'bubbles',
    sort_order  INT          NOT NULL DEFAULT 0,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Grille tarifaire (inscriptions et tarifs)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pricing (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    label      VARCHAR(150) NOT NULL,
    detail     VARCHAR(150) DEFAULT NULL,
    price      VARCHAR(50)  NOT NULL,
    sort_order INT          NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- Données initiales — reprennent le contenu du site actuel
-- =====================================================================

INSERT INTO site_content (content_key, content_value) VALUES
('site_name',        'Plongée Carpentras - CSCV'),
('hero_title',        'CLUB SUBAQUATIQUE DU COMTAT VENAISSIN'),
('hero_subtitle',     'Découvrez les merveilles du monde sous-marin'),
('hero_cta_text',     'Rejoignez-nous sur Facebook'),
('facebook_url',      'https://www.facebook.com/PlongeeCarpentras'),
('contact_address',   'Piscine municipale, Rue du Mont de Piété, 84200 Carpentras'),
('contact_email',     'contact@plongeecarpentras.fr'),
('association_name',  'Club Subaquatique du Comtat Venaissin'),
('footer_text',       '© 2026 Plongée Carpentras - CSCV. Tous droits réservés.'),
('pricing_note',      '*membre du bureau directeur / encadrant noté au planning.'),
('mentions_legales',  'Alors que nous avons effectué toutes les démarches pour nous assurer de la fiabilité des informations contenues sur ce site Internet, l’association Club Subaquatique du Comtat Venaissin (CSCV) ne peut encourir aucune responsabilité du fait d’erreurs, d’omissions, ou pour les résultats qui pourraient être obtenus par l’usage de ces informations.\n\nArticle L122-4 du Code de la Propriété Intellectuelle :\n« Toute représentation ou reproduction intégrale ou partielle faite sans le consentement de l’auteur ou de ses ayants droit ou ayants cause est illicite. Il en est de même pour la traduction, l’adaptation ou la transformation, l’arrangement ou la reproduction par un art ou un procédé quelconque. Ils peuvent également, s’ils le souhaitent, signaler que les contenus peuvent être reproduits, mais avec mention de la source et éventuellement un lien pointant vers le contenu original. »')
ON DUPLICATE KEY UPDATE content_value = VALUES(content_value);

INSERT INTO formations (title, summary, details, icon, sort_order) VALUES
('Plongée Enfants', 'Pour les 10-14 ans. Découvrez le monde sous-marin en toute sécurité.', 'Une initiation ludique et encadrée pour les jeunes plongeurs, en piscine, dans le respect du rythme de chacun.', 'child', 1),
('Plongée Sportive en Piscine', 'Participez à des compétitions de plongée sportive en piscine.', 'Entraînements réguliers et participation aux compétitions régionales et nationales de nage avec palmes et apnée sportive.', 'medal', 2),
('Baptême de Plongée', 'Votre première expérience sous-marine accompagné par nos encadrants.', 'Une immersion encadrée par un moniteur diplômé, sans prérequis, pour découvrir les sensations de la plongée.', 'first-dive', 3),
('Initiateur', 'Formez-vous à l’enseignement de la plongée en piscine et en mer jusqu’à 6 mètres.', 'Formation d’encadrant permettant d’enseigner la plongée en milieu naturel jusqu’à 6 mètres, sous la responsabilité d’un directeur de plongée.', 'whistle', 4),
('Niveau 1', 'Apprenez les bases de la plongée jusqu’à 20 mètres.', 'Le premier niveau fédéral : autonomie encadrée jusqu’à 20 mètres et acquisition des bases de la sécurité en plongée.', 'fin', 5),
('Niveau 2', 'Perfectionnez vos compétences pour des plongées plus profondes et gagnez en autonomie.', 'Accès à l’autonomie encadrée jusqu’à 20 mètres et à la plongée guidée jusqu’à 40 mètres.', 'compass', 6),
('Niveau 3', 'Accessible dès 18 ans. Plongez en autonomie entre 0 et 60 mètres.', 'Le niveau de l’autonomie complète en exploration, de la surface jusqu’à 60 mètres.', 'depth-gauge', 7),
('Niveau 4', 'Accessible dès 18 ans. Plongez en autonomie jusqu’à 60 mètres et encadrez d’autres plongeurs.', 'Le niveau guide de palanquée : encadrement de plongeurs en exploration jusqu’à 60 mètres.', 'anchor', 8)
ON DUPLICATE KEY UPDATE title = VALUES(title);

INSERT INTO pricing (label, detail, price, sort_order) VALUES
('Adhésion adulte (+16 ans) + formation (N1/N2)', '48,50 € + 120 € + 66 €', '234,50 €', 1),
('Adhésion adulte', '48,50 € + 120 €', '168,50 €', 2),
('Adhésion étudiant + formation', '48,50 € + 90 € + 66 €', '204,50 €', 3),
('Adhésion étudiant', '48,50 € + 90 €', '138,50 €', 4),
('Adhésion cadet (12-16 ans) + formation (N1/N2)', '30,50 € + 90 € + 66 €', '186,50 €', 5),
('Adhésion cadet', '30,50 € + 90 €', '119,50 €', 6),
('Adhésion enfant (-12 ans)', '14 € + 90 €', '104 €', 7),
('Adhésion Staff*', '48,50 € + 60 €', '108,50 €', 8);

INSERT INTO events (title, event_date, event_time, description) VALUES
('Réunion de rentrée', DATE_ADD(CURDATE(), INTERVAL 14 DAY), '18:30', 'Présentation de la saison et des formations à la piscine municipale.'),
('Sortie plongée mer', DATE_ADD(CURDATE(), INTERVAL 30 DAY), '08:00', 'Sortie encadrée en Méditerranée, ouverte aux niveaux 1 et plus.'),
('Baptêmes de plongée', DATE_ADD(CURDATE(), INTERVAL 45 DAY), '14:00', 'Séance de baptêmes ouverte au public à la piscine municipale, sur inscription.');
