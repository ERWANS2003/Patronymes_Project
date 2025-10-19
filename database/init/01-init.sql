-- Script d'initialisation de la base de données PostgreSQL
-- Ce script s'exécute automatiquement lors du premier démarrage du conteneur

-- Créer la base de données si elle n'existe pas déjà
SELECT 'CREATE DATABASE patronymes'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'patronymes')\gexec

-- Connecter à la base de données patronymes
\c patronymes;

-- Créer l'utilisateur si il n'existe pas déjà
DO
$do$
BEGIN
   IF NOT EXISTS (
      SELECT FROM pg_catalog.pg_roles
      WHERE  rolname = 'patronymes') THEN

      CREATE ROLE patronymes LOGIN PASSWORD 'password';
   END IF;
END
$do$;

-- Accorder les privilèges
GRANT ALL PRIVILEGES ON DATABASE patronymes TO patronymes;
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO patronymes;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO patronymes;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO patronymes;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO patronymes;

-- Configurer les paramètres de locale
SET lc_collate = 'fr_FR.UTF-8';
SET lc_ctype = 'fr_FR.UTF-8';
