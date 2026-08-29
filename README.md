# Plongée Carpentras — CSCV

Site vitrine du Club Subaquatique du Comtat Venaissin : une page unique
(hero, le club en 3 chiffres, formations, tarifs, contact) + 2 sous-pages
(calendrier, inscription), pensé mobile d'abord pour les visiteurs qui
arrivent depuis Facebook. Direction artistique « descente en profondeur » :
le fond s'assombrit progressivement au scroll (bleu profond → turquoise),
avec un indicateur de profondeur discret (0 m / 20 m / 60 m) corrélé aux
niveaux de formation. Un backoffice permet de tout gérer sans toucher au
code (calendrier, formations, tarifs, documents PDF, textes du site).

Stack : **PHP + MySQL** côté serveur (sans framework ni étape de build —
compatible avec n'importe quel hébergement mutualisé, y compris le forfait
le plus basique d'OVH), **GSAP + ScrollTrigger + Lenis** en CDN côté client
pour les animations (apparitions en stagger réversibles, parallaxe légère,
smooth scroll). Si ces CDN ne chargent pas (bloqueur de script, hors ligne),
le site se dégrade proprement : scroll natif et apparitions via
IntersectionObserver, contenu toujours visible même sans JavaScript.

## Structure du projet

```
config/db.php        Connexion à la base (identifiants à renseigner)
database/schema.sql   Tables + contenu initial à importer
includes/             Fonctions PHP, header/footer, icônes SVG partagées
assets/css/style.css  Design system (palette profondeur, typo, animations)
assets/js/main.js     Nav, popups, calendrier, Lenis/GSAP, indicateur de profondeur
uploads/documents/    PDF uploadés depuis le backoffice (non versionnés)
index.php              Page unique : hero, chiffres, formations, tarifs, contact
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
  présentation du club, coordonnées, SEO, mentions légales...).
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
