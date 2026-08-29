# Plongée Carpentras — CSCV

Site du Club Subaquatique du Comtat Venaissin : 5 pages publiques (accueil,
calendrier, formations, inscriptions & tarifs, contact) entièrement
dynamiques, plus un backoffice pour tout gérer sans toucher au code
(calendrier, formations, tarifs, textes du site).

Stack : **PHP + MySQL**, sans framework ni étape de build — compatible avec
n'importe quel hébergement mutualisé, y compris le forfait le plus basique
d'OVH (PHP + 1 base MySQL sont inclus d'office, aucune option payante
nécessaire).

## Structure du projet

```
config/db.php        Connexion à la base (identifiants à renseigner)
database/schema.sql   Tables + contenu initial à importer
includes/             Fonctions PHP, header/footer, icônes SVG partagées
assets/css, assets/js Styles et scripts du site public
index.php, calendrier.php, formations.php,
inscription.php, contact.php             Pages publiques
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
- **Formations** : gérer les cartes de la page Formations (titre, résumé,
  détails affichés en popup, icône, ordre d'affichage).
- **Tarifs** : gérer les lignes du tableau d'inscriptions.
- **Contenu du site** : modifier les textes (titre et sous-titre de
  l'accueil, adresse, email, lien Facebook, mentions légales...).
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
