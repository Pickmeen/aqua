# Tester puis mettre en ligne

Ce document décrit comment faire tourner une **préproduction** (une copie
du site, en ligne, invisible pour Google et protégée par mot de passe),
puis comment basculer en production une fois que tout est validé.

## En deux mots

| | Combien de temps | Ce qui prend le temps |
|---|---|---|
| Mettre la préprod en place | **20 à 30 min** la première fois | La création de la base de données par OVH (5 à 30 min, en arrière-plan) |
| Tester tranquillement | Le temps que vous voulez | — |
| **Basculer en production** | **5 à 10 min** | Uniquement l'envoi des fichiers par FTP |
| Revenir en arrière si problème | **5 min** | Réenvoyer la sauvegarde de l'ancien site |

La bascule est rapide **parce que la base de données n'a pas besoin d'être
migrée** : la refonte n'ajoute aucune table ni aucune colonne, et les deux
seuls nouveaux textes (`hero_eyebrow`, `contact_hours`) ont une valeur de
repli affichée automatiquement tant qu'ils ne sont pas renseignés. Le
nouveau code fonctionne donc tel quel sur la base actuelle du site.

Une fois la préprod en place, elle est réutilisable : les mises à jour
suivantes se testent en réenvoyant simplement les fichiers dans
`/preprod/`.

---

## 1. Mettre la préproduction en place

### Pourquoi un sous-dossier plutôt qu'un sous-domaine

On installe la préprod dans `https://www.plongeecarpentras.fr/preprod/`
plutôt que sur `preprod.plongeecarpentras.fr`. Raison : un sous-domaine
demande une entrée DNS **et** un nouveau certificat SSL, ce qui peut
prendre plusieurs heures chez OVH. Un sous-dossier fonctionne
immédiatement, avec le certificat existant.

### 1.1 Créer une base de données de test

Dans le Manager OVH : *Hébergements > votre hébergement > Bases de
données > Créer une base de données*. Notez le serveur, le nom,
l'utilisateur et le mot de passe.

> **Si votre offre n'autorise qu'une seule base** : vous pouvez faire
> pointer la préprod sur la base de production **à condition de ne rien
> enregistrer depuis le backoffice de la préprod** — vous modifieriez le
> vrai site. Dans ce cas, testez uniquement l'affichage public, et testez
> le backoffice plus tard, après la bascule.

La création est asynchrone : OVH vous prévient par e-mail quand la base
est prête. C'est l'étape la plus longue, et la seule où il faut attendre.

### 1.2 Envoyer les fichiers

Par FTP/SFTP, créez un dossier `preprod` à la racine de votre hébergement
(à côté de `index.php`), et copiez-y **tout le contenu du dépôt** :
`index.php`, `calendrier.php`, `inscription.php`, `admin/`, `assets/`,
`config/`, `database/`, `includes/`, `uploads/`, `.htaccess`.

### 1.3 Marquer le dossier comme préproduction

Créez à la racine de `/preprod/` un fichier **vide** nommé :

```
preprod.flag
```

Sa seule présence suffit. Le site détecte alors qu'il est en test et :

- ajoute `<meta name="robots" content="noindex, nofollow">` — Google ne
  référencera jamais la préprod ;
- **désactive Google Analytics** — vos visites de test ne polluent pas les
  statistiques du vrai site ;
- affiche un bandeau orange « Préproduction » en haut de chaque page, pour
  qu'on ne confonde jamais les deux.

Ce fichier n'est pas versionné dans Git : il ne peut donc pas partir en
production par accident.

### 1.4 Renseigner la connexion à la base

Éditez `preprod/config/db.php` **sur le serveur** avec les identifiants de
la base de test créée à l'étape 1.1 :

```php
define('DB_HOST', 'xxxxxxx.mysql.db');
define('DB_NAME', 'xxxxxxx');
define('DB_USER', 'xxxxxxx');
define('DB_PASS', 'xxxxxxx');
```

### 1.5 Remplir la base de test

Deux possibilités, au choix :

**a) Repartir du contenu par défaut** — dans phpMyAdmin, sélectionnez la
base de test, onglet *Importer*, choisissez `database/schema.sql`.

**b) Travailler sur une copie du contenu réel** (recommandé, c'est le test
le plus fidèle) — dans phpMyAdmin, sélectionnez la base **de
production**, onglet *Exporter*, format SQL, téléchargez le fichier. Puis
sélectionnez la base **de test**, onglet *Importer*, envoyez ce fichier.
Vous testez alors la refonte avec les vrais textes, vraies formations,
vrais tarifs et vraies dates.

### 1.6 Protéger l'accès par mot de passe

Créez `preprod/.htaccess` contenant :

```apache
AuthType Basic
AuthName "Preproduction - acces reserve"
AuthUserFile /home/VOTRE_LOGIN/www/preprod/.htpasswd
Require valid-user
```

Remplacez le chemin par le **chemin absolu** de votre hébergement. Pour le
trouver : dans le Manager OVH, le login FTP vous donne la racine (souvent
`/homez.NNN/VOTRELOGIN/www/`). En cas de doute, créez temporairement un
fichier `preprod/chemin.php` contenant `<?php echo __DIR__;` , ouvrez-le
dans le navigateur, notez le chemin affiché, puis **supprimez ce fichier**.

Créez ensuite `preprod/.htpasswd` avec une ligne `identifiant:mot_de_passe_hashé`.
Le hash se génère avec n'importe quel générateur htpasswd en ligne, ou en
SSH avec `htpasswd -nb club motdepasse`.

> Si la protection par mot de passe vous bloque, vous pouvez vous en
> passer : le bandeau et le `noindex` empêchent déjà toute confusion et
> tout référencement. Le mot de passe évite simplement qu'un visiteur
> tombe dessus par hasard.

### 1.7 Créer un compte administrateur de test

Ouvrez `https://www.plongeecarpentras.fr/preprod/admin/setup.php` et
créez un compte. (Si vous avez importé une copie de la base de
production à l'étape 1.5b, les comptes existants fonctionnent déjà et
cette page est désactivée.)

### 1.8 Vérifier

Ouvrez `https://www.plongeecarpentras.fr/preprod/` : le bandeau orange
doit être visible. Voir la liste de contrôle plus bas.

---

## 2. Ce qu'il faut vérifier avant de basculer

- [ ] Le bandeau « Préproduction » est bien affiché (sinon, le
      `preprod.flag` est absent : ne basculez pas tant que ce n'est pas
      corrigé, sinon vos tests fausseront les statistiques).
- [ ] Page d'accueil : le prochain rendez-vous s'affiche avec le bon
      compte à rebours.
- [ ] Les filtres des formations réagissent, une fiche s'ouvre et se
      ferme (croix, clic à côté, touche Échap).
- [ ] Le tableau des tarifs est lisible sur téléphone.
- [ ] Calendrier : le bon mois s'affiche, les jours d'événement sont
      colorés et cliquables, les flèches changent de mois.
- [ ] Inscription : les liens de téléchargement des PDF fonctionnent.
- [ ] Le bouton lune/soleil bascule le thème, et le choix est conservé
      après rechargement.
- [ ] Sur téléphone : le menu s'ouvre et se referme, la barre d'actions
      du bas est présente.
- [ ] Backoffice : connexion, modification d'un texte, ajout d'un
      événement, upload d'un PDF.
- [ ] Testez sur un vrai téléphone, pas seulement en réduisant la fenêtre.

---

## 3. Basculer en production

### 3.1 Sauvegarder d'abord (indispensable)

1. **Les fichiers** : par FTP, téléchargez la racine actuelle du site dans
   un dossier local daté, par exemple `sauvegarde-2026-09-01/`. C'est ce
   qui vous permettra de revenir en arrière en 5 minutes.
2. **La base** : dans phpMyAdmin, base de production, onglet *Exporter*,
   téléchargez le fichier SQL. (La bascule ne touche pas la base, mais une
   sauvegarde ne coûte rien.)

### 3.2 Envoyer les fichiers

Copiez à la racine du site, en écrasant les fichiers existants :

```
index.php  calendrier.php  inscription.php  formations.php  contact.php
.htaccess
assets/    includes/    admin/    database/
```

**Ne touchez pas à** :

- `config/db.php` — il contient les identifiants de la base de
  production. Ne l'écrasez surtout pas avec celui de la préprod.
- `uploads/` — ce sont vos PDF déjà en ligne.

**N'envoyez pas** `preprod.flag` à la racine du site. S'il s'y trouvait,
le vrai site s'afficherait avec le bandeau orange et sans statistiques.

Un fichier de l'ancienne version n'est plus utilisé et peut être
supprimé du serveur : `assets/js/three-effects.js`.

### 3.3 Vérifier immédiatement

Ouvrez le site en **navigation privée** (pour éviter le cache) :

- Aucun bandeau orange.
- Le site s'affiche avec la nouvelle mise en page.
- Le backoffice répond sur `/admin/`.

Si l'affichage semble être resté à l'ancienne version, c'est le cache du
navigateur : rechargez avec `Ctrl+F5` (`Cmd+Shift+R` sur Mac).

### 3.4 Après la bascule

Dans le backoffice, *Contenu du site*, deux nouveaux champs sont
disponibles et méritent d'être renseignés :

- **Petite ligne au-dessus du titre** — actuellement « Carpentras ·
  Vaucluse · Club FFESSM ».
- **Créneaux / horaires** — les jours et heures d'entraînement, affichés
  dans la section Contact. C'est l'information qu'on nous demande le plus
  souvent après les tarifs.

Le titre principal du hero reste celui de votre base actuelle. Si c'est
encore « CLUB SUBAQUATIQUE DU COMTAT VENAISSIN », pensez à le remplacer
par une phrase qui parle à un débutant, par exemple « Apprenez à plonger,
à Carpentras » — le nom complet de l'association reste affiché dans le
pied de page et dans les données de référencement.

---

## 4. Revenir en arrière

Si quelque chose ne va pas, réenvoyez par FTP le contenu du dossier de
sauvegarde de l'étape 3.1 par-dessus la racine du site. Comme la base de
données n'a pas été modifiée, l'ancien site refonctionne immédiatement,
avec tout son contenu.
