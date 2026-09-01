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
    depth_label VARCHAR(20)  NOT NULL DEFAULT 'Piscine',
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

-- ---------------------------------------------------------------------
-- Documents téléchargeables (fiche d'adhésion, CACI, autorisation
-- parentale...) affichés sur la page Inscription. Le fichier est
-- uploadé depuis le backoffice ; doc_key identifie l'emplacement fixe
-- où chaque document s'affiche.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS documents (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    doc_key       VARCHAR(50)  NOT NULL UNIQUE,
    title         VARCHAR(150) NOT NULL,
    description   VARCHAR(255) DEFAULT NULL,
    filename      VARCHAR(255) DEFAULT NULL,
    original_name VARCHAR(255) DEFAULT NULL,
    sort_order    INT          NOT NULL DEFAULT 0,
    uploaded_at   TIMESTAMP    NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- Données initiales — reprennent le contenu du site actuel
-- =====================================================================

INSERT INTO site_content (content_key, content_value) VALUES
('site_name',        'Plongée Carpentras - CSCV'),
('hero_eyebrow',      'Carpentras · Vaucluse · Club affilié FFESSM'),
('hero_title',        'Apprenez à plonger, à Carpentras'),
('hero_subtitle',     'Baptêmes, formations fédérales et sorties en mer, encadrés par des moniteurs diplômés. Du premier souffle sous l’eau au niveau d’encadrement.'),
('hero_cta_baptism',  'Baptême de plongée'),
('hero_cta_join',     'Nous rejoindre'),
('hero_cta_text',     'Rejoignez-nous sur Facebook'),
('facebook_url',      'https://www.facebook.com/PlongeeCarpentras'),
('contact_address',   'Piscine municipale, Rue du Mont de Piété, 84200 Carpentras'),
('contact_email',     'contact@plongeecarpentras.fr'),
('contact_hours',    'Entraînements à la piscine municipale pendant la saison. Consultez le calendrier pour les créneaux et les dates de sortie.'),
('association_name',  'Club Subaquatique du Comtat Venaissin'),
('footer_text',       '© 2026 Plongée Carpentras - CSCV. Tous droits réservés.'),
('pricing_note',      '*membre du bureau directeur / encadrant noté au planning.'),
('club_intro',        'Affilié à la FFESSM, le Club Subaquatique du Comtat Venaissin accueille à Carpentras les plongeurs de tous âges et de tous niveaux, du baptême aux formations d’encadrement, en piscine comme en mer.'),
('stat_1_value',      '8'),
('stat_1_label',      'formations proposées'),
('stat_2_value',      '10 ans'),
('stat_2_label',      'âge minimum pour rejoindre le club'),
('stat_3_value',      '60 m'),
('stat_3_label',      'profondeur atteinte en autonomie (N3/N4)'),
('seo_title',         'Plongée Carpentras — Club Subaquatique du Comtat Venaissin (FFESSM)'),
('seo_description',   'Club de plongée sous-marine FFESSM à Carpentras (Vaucluse) : baptêmes, formations enfants et adultes, niveaux 1 à 4, sorties mer. Rejoignez le Club Subaquatique du Comtat Venaissin.'),
('mentions_legales',  'Alors que nous avons effectué toutes les démarches pour nous assurer de la fiabilité des informations contenues sur ce site Internet, l’association Club Subaquatique du Comtat Venaissin (CSCV) ne peut encourir aucune responsabilité du fait d’erreurs, d’omissions, ou pour les résultats qui pourraient être obtenus par l’usage de ces informations.\n\nArticle L122-4 du Code de la Propriété Intellectuelle :\n« Toute représentation ou reproduction intégrale ou partielle faite sans le consentement de l’auteur ou de ses ayants droit ou ayants cause est illicite. Il en est de même pour la traduction, l’adaptation ou la transformation, l’arrangement ou la reproduction par un art ou un procédé quelconque. Ils peuvent également, s’ils le souhaitent, signaler que les contenus peuvent être reproduits, mais avec mention de la source et éventuellement un lien pointant vers le contenu original. »')
ON DUPLICATE KEY UPDATE content_value = VALUES(content_value);

INSERT INTO formations (title, summary, details, icon, depth_label, sort_order) VALUES
('Plongée Enfants', 'Pour les 10-14 ans. Découvrez le monde sous-marin en toute sécurité.', 'Une initiation ludique et encadrée pour les jeunes plongeurs, en piscine, dans le respect du rythme de chacun.', 'child', 'Piscine', 1),
('Plongée Sportive en Piscine', 'Participez à des compétitions de plongée sportive en piscine.', 'Entraînements réguliers et participation aux compétitions régionales et nationales de nage avec palmes et apnée sportive.', 'medal', 'Piscine', 2),
('Baptême de Plongée', 'Votre première expérience sous-marine accompagné par nos encadrants.', 'Une immersion encadrée par un moniteur diplômé, sans prérequis, pour découvrir les sensations de la plongée.', 'first-dive', '6 m', 3),
('Initiateur', 'Formez-vous à l’enseignement de la plongée en piscine et en mer jusqu’à 6 mètres.', 'Formation d’encadrant permettant d’enseigner la plongée en milieu naturel jusqu’à 6 mètres, sous la responsabilité d’un directeur de plongée.', 'whistle', '6 m', 4),
('Niveau 1', 'Apprenez les bases de la plongée jusqu’à 20 mètres.', 'Le premier niveau fédéral : autonomie encadrée jusqu’à 20 mètres et acquisition des bases de la sécurité en plongée.', 'fin', '20 m', 5),
('Niveau 2', 'Perfectionnez vos compétences pour des plongées plus profondes et gagnez en autonomie.', 'Accès à l’autonomie encadrée jusqu’à 20 mètres et à la plongée guidée jusqu’à 40 mètres.', 'compass', '40 m', 6),
('Niveau 3', 'Accessible dès 18 ans. Plongez en autonomie entre 0 et 60 mètres.', 'Le niveau de l’autonomie complète en exploration, de la surface jusqu’à 60 mètres.', 'depth-gauge', '60 m', 7),
('Niveau 4', 'Accessible dès 18 ans. Plongez en autonomie jusqu’à 60 mètres et encadrez d’autres plongeurs.', 'Le niveau guide de palanquée : encadrement de plongeurs en exploration jusqu’à 60 mètres.', 'anchor', '60 m', 8)
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

INSERT INTO documents (doc_key, title, description, sort_order) VALUES
('fiche_adhesion',        'Fiche d’adhésion', 'À remplir et à apporter le jour de votre inscription.', 1),
('caci',                  'Certificat médical (CACI)', 'Certificat d’Absence de Contre-indication à la plongée, à faire établir par un médecin.', 2),
('autorisation_parentale','Autorisation parentale', 'Obligatoire pour les adhérents mineurs, à faire signer par un responsable légal.', 3)
ON DUPLICATE KEY UPDATE title = VALUES(title);
