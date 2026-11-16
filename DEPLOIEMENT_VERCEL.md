# Déploiement de Patronymes sur Vercel

Le site est en ligne : https://patronymes-project.vercel.app

## Comment c'est monté

- **PHP** : Vercel n'exécute pas PHP nativement. Le projet passe par le runtime
  communautaire `vercel-php@0.7.4` (PHP 8.3), déclaré dans `vercel.json`.
  `api/index.php` reçoit toutes les requêtes ; `public/build`, `public/images` et
  quelques fichiers statiques sont servis directement.
- **Base de données** : PostgreSQL chez Neon, créée depuis le tableau de bord Vercel
  (onglet « Storage ») et reliée au projet. Vercel fournit ses variables
  (`DATABASE_URL_UNPOOLED`, etc.) ; `config/database.php` utilise la connexion
  directe, la connexion « poolée » faisant échouer les migrations.
- **Variables à part** : `APP_KEY` et `LOG_STDERR_FORMATTER` sont définies dans les
  réglages du projet Vercel. Tout le reste est dans `vercel.json`.
- **Disque en lecture seule** : caches, vues compilées et journaux vont dans `/tmp`
  (`APP_*_CACHE`, `VIEW_COMPILED_PATH`, `LOG_PATH`), les sessions dans un cookie.

## À chaque déploiement

Le script `vercel` de `composer.json` lance pendant le build :

1. `php artisan migrate --force`
2. `php artisan app:import-local-data` : charge `database/data/export.json` si la
   base est vide, sinon ne fait rien.
3. `php artisan cache:clear database`

Pour redéployer depuis le PC : `vercel deploy --prod`.

## Données

La base en ligne est une copie complète de la base SQLite locale au 16/11/2026
(71 patronymes, 23 comptes, données géographiques, contributions), faite avec :

    php artisan app:export-local-data     # écrit database/data/export.json
    vercel deploy --prod                  # le build importe le fichier

`database/data/export.json` contient les comptes et leurs mots de passe chiffrés :
il est exclu de git et ne doit pas y entrer. L'import ne se relance pas tant que la
base contient des données ; pour réimporter, il faut d'abord vider la base Neon.

## Limites connues

- Les fichiers écrits par l'application (import Excel, photos de profil,
  sauvegardes) vont dans `/tmp` et disparaissent : ces fonctions ne sont pas fiables.
- Pas de file d'attente ni de tâches planifiées (`QUEUE_CONNECTION=sync`).
- Premier chargement lent après une période d'inactivité.
- Les journaux sont visibles dans l'onglet « Logs » du projet Vercel.
