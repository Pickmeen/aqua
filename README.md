# Plongée Carpentras — CSCV

Site du Club Subaquatique du Comtat Venaissin : une page d'accueil
(hero, parcours d'entrée, chiffres clés, formations, tarifs, contact) et
deux sous-pages (calendrier, inscription), pensées mobile d'abord pour les
visiteurs qui arrivent depuis Facebook. Un backoffice permet de tout gérer
sans toucher au code.

## Parti pris de l'interface

**Clair et lisible d'abord.** Fond clair par défaut, **thème sombre
automatique** (préférence système) avec bascule manuelle mémorisée. Grandes
zones cliquables, contrastes vérifiés, tout est utilisable au clavier.

**Le visiteur est guidé.** La page d'accueil répond dans l'ordre aux trois
questions qu'on nous pose : *c'est quand ?* (le prochain rendez-vous est
affiché dès le hero, avec un compte à rebours), *c'est pour moi ?* (trois
parcours d'entrée explicites, puis des formations filtrables), *combien ?*
(un tableau de tarifs où chaque ligne est un total, sans supplément caché).

**Zéro dépendance JavaScript externe.** GSAP, Lenis et Three.js ont été
retirés au profit des capacités natives du navigateur :

| Effet | Comment |
|---|---|
| Apparitions au scroll | `animation-timeline: view()` en CSS pur, repli `IntersectionObserver` |
| Transitions entre pages | `@view-transition` (View Transitions multi-pages) |
| Fenêtres de détail | `<dialog>` natif (focus piégé et `Échap` gérés par le navigateur) |
| Menu mobile | sélecteur `:has()` sur l'état `aria-expanded` |
| Cartes adaptatives | container queries (`@container`) |
| Couleurs | `oklch()` avec repli hexadécimal, `color-mix()` |
| Barre de progression du HUD | `@property` + transition d'une propriété personnalisée |

**Le relief 3D et le HUD sont faits maison.** Le décor du hero est un
relief sous-marin en fil de fer, projeté en perspective et dessiné dans un
`<canvas>` 2D (`assets/js/hero-scene.js`, quelques kilo-octets au lieu des
~600 Ko de Three.js). L'« ordinateur de plongée » en bas à droite affiche la
progression dans la page sous forme de profondeur, la section courante et
le prochain rendez-vous. Les deux s'arrêtent hors écran, en onglet inactif,
et respectent `prefers-reduced-motion`.

**Dégradation propre.** Sans JavaScript, sans polices Google ou sur un
navigateur ancien, tout le contenu reste visible et navigable : les
apparitions ne masquent rien tant que le script n'a pas confirmé qu'il a
pris la main, et le canvas est purement décoratif.

Stack : **PHP + MySQL** sans framework ni étape de build — compatible avec
n'importe quel hébergement mutualisé, y compris le forfait le plus basique
d'OVH.

## Structure du projet

```
config/db.php        Connexion à la base (identifiants à renseigner)
database/schema.sql   Tables + contenu initial à importer
includes/             Fonctions PHP, header/footer, icônes SVG partagées
assets/css/style.css  Design system (tokens, thèmes clair/sombre, composants)
assets/js/main.js     Nav, thème, filtres, dialogues, calendrier, HUD
assets/js/hero-scene.js  Décor 3D du hero (canvas 2D, projection perspective)
uploads/documents/    PDF uploadés depuis le backoffice (non versionnés)
index.php              Accueil : hero, parcours, chiffres, formations, tarifs, contact
calendrier.php, inscription.php    Sous-pages
formations.php, contact.php        Redirections 301 vers les ancres de index.php
admin/                Backoffice (protégé par mot de passe)
```

## 1. Déploiement sur l'hébergement OVH

1. **Créer la base de données** dans le Manager OVH (Hébergements >
   Bases de données > Créer une base MySQL). Note le host, le nom de la
   base, l'utilisateur et le mot de passe fournis.
2. **Importer le schéma** : ouvre phpMyAdmin depuis le Manager OVH, choisis
   ta base, onglet *Importer*, sélectionne `database/schema.sql`, valide.
   Cela crée les tables et reprend le contenu actuel du site (formations,
   tarifs, textes) comme point de départ.
3. **Envoyer les fichiers** sur le serveur par FTP/SFTP (identifiants dans
   le Manager OVH), à la racine de ton hébergement (ou dans un
   sous-dossier si le site n'est pas à la racine).
4. **Configurer la connexion** : édite `config/db.php` **directement sur le
   serveur** (pas dans le dépôt Git) et renseigne les 4 constantes avec les
   identifiants obtenus à l'étape 1 :
   ```php
   define('DB_HOST', 'xxxxxxx.mysql.db');
   define('DB_NAME', 'xxxxxxx');
   define('DB_USER', 'xxxxxxx');
   define('DB_PASS', 'xxxxxxx');
   ```
5. **Créer le premier compte administrateur** : va sur
   `https://tondomaine.fr/admin/setup.php`, choisis un identifiant et un
   mot de passe (10 caractères minimum). Cette page se désactive
   automatiquement dès qu'un compte existe — tu peux ensuite la supprimer
   du serveur par sécurité (pas obligatoire, mais recommandé).
6. Connecte-toi sur `https://tondomaine.fr/admin/login.php`.

Le site n'a besoin d'aucun accès SSH, ni de tâche cron, ni de composeur —
tout fonctionne avec un simple hébergement PHP + MySQL basique.

## 2. Utiliser le backoffice

Depuis `/admin/`, une fois connecté :

- **Calendrier** : ajouter, modifier, supprimer des événements (titre,
  date, heure, description). Le calendrier et la liste « à venir » du site
  public se mettent à jour immédiatement.
- **Formations** : gérer les cartes de la section Formations (titre,
  résumé, détails affichés dans le panneau latéral, icône, profondeur
  affichée, ordre d'affichage — profondeur croissante recommandée).
- **Tarifs** : gérer les lignes du tableau d'inscriptions.
- **Documents (PDF)** : uploader/remplacer/retirer les PDF proposés sur la
  page Inscription (fiche d'adhésion, CACI, autorisation parentale). Seuls
  les fichiers `.pdf` sont acceptés (10 Mo max, type MIME vérifié). Tant
  qu'un document n'est pas uploadé, la page affiche « à venir » au lieu
  d'un lien mort.
- **Contenu du site** : modifier les textes (hero, chiffres clés,
  présentation du club, coordonnées, créneaux, SEO, mentions légales...).
  Les filtres de la section Formations (« Je débute », « En piscine »,
  « En mer », « Encadrement ») sont déduits automatiquement du titre, de
  l'icône et de la profondeur de chaque formation : rien à cocher.
- **Mon compte** : changer l'identifiant ou le mot de passe.

## Mot de passe oublié

Si tu perds l'accès au backoffice et qu'il n'y a qu'un seul compte, tu
peux le réinitialiser sans SSH :
1. Dans phpMyAdmin, vide la table `admin_users` (onglet *Vider*).
2. Retourne sur `/admin/setup.php` pour recréer un compte.

## Sécurité

- Mots de passe stockés hachés (`password_hash`), jamais en clair.
- Requêtes SQL préparées (PDO) partout — pas d'injection possible.
- Formulaires admin protégés par jeton CSRF et throttling anti brute-force
  sur la connexion.
- `config/`, `database/` et les dossiers `includes/` sont bloqués à l'accès
  web direct via `.htaccess`.
- Les PDF uploadés sont validés (extension + type MIME réel) et le dossier
  `uploads/documents/` interdit toute exécution de script via `.htaccess`.
- Les pages `/admin/*` sont marquées `noindex` et nécessitent une session
  authentifiée.

## Développement local

Avec PHP installé (8.x recommandé) et une base MySQL/MariaDB locale :

```bash
php -S localhost:8000
```

Configure `config/db.php` avec les identifiants de ta base locale, importe
`database/schema.sql`, puis ouvre `http://localhost:8000/admin/setup.php`
pour créer un compte de test.

Aucune étape de build : les fichiers CSS et JS sont servis tels quels.

> **Attention en cas de réimport.** `database/schema.sql` réécrit les textes
> du site (`ON DUPLICATE KEY UPDATE`). Sur une base déjà en production, ne
> réimporte que si tu acceptes de repartir des textes par défaut — les
> nouveaux réglages (`hero_eyebrow`, `contact_hours`) ont de toute façon une
> valeur de repli affichée tant qu'ils ne sont pas renseignés.
