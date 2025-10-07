# 🗄️ Guide de Chargement des Données dans PostgreSQL

## ✅ **Problème des Doublons Résolu**

**Avant** : 35 patronymes avec 17 uniques (18 doublons)
**Maintenant** : 17 patronymes uniques avec contrainte d'unicité

## 🔧 **Solutions Appliquées**

### **1. Suppression des Doublons**

-   ✅ **18 doublons supprimés** automatiquement
-   ✅ **Garde le meilleur** : Plus de vues → Plus récent
-   ✅ **Contrainte d'unicité** ajoutée sur le nom

### **2. Patronymes Nettoyés**

```
Supprimés : Compaoré, Tankoano, Zongo (x2), Kabré, Zerbo,
           Bationo, Sawadogo (x3), Bamba, Ouattara,
           Kaboré (x2), Traoré (x2), Ouédraogo (x2)
```

## 📊 **Méthodes de Chargement PostgreSQL**

### **Méthode 1 : Laravel Migrations + Seeders (Recommandée)**

#### **1.1. Configuration PostgreSQL**

```bash
# Vérifier la configuration dans .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=patronymes_db
DB_USERNAME=postgres
DB_PASSWORD=votre_mot_de_passe
```

#### **1.2. Créer la base de données**

```sql
-- Se connecter à PostgreSQL
psql -U postgres

-- Créer la base de données
CREATE DATABASE patronymes_db;

-- Créer un utilisateur (optionnel)
CREATE USER patronymes_user WITH PASSWORD 'mot_de_passe_securise';
GRANT ALL PRIVILEGES ON DATABASE patronymes_db TO patronymes_user;
```

#### **1.3. Exécuter les migrations**

```bash
# Dans le dossier patronymes-app
php artisan migrate:fresh --seed
```

#### **1.4. Vérifier les données**

```bash
# Compter les patronymes
php artisan tinker --execute="echo App\Models\Patronyme::count();"

# Lister les patronymes
php artisan tinker --execute="App\Models\Patronyme::all(['nom'])->each(function(\$p) { echo \$p->nom . PHP_EOL; });"
```

### **Méthode 2 : Import SQL Direct**

#### **2.1. Générer un fichier SQL**

```bash
# Créer un export SQL
php artisan db:dump --file=patronymes_data.sql

# Ou utiliser pg_dump
pg_dump -U postgres -h localhost patronymes_db > patronymes_backup.sql
```

#### **2.2. Importer dans PostgreSQL**

```bash
# Importer le fichier SQL
psql -U postgres -d patronymes_db -f patronymes_data.sql
```

### **Méthode 3 : Utilisation de pgAdmin**

#### **3.1. Interface Graphique**

1. Ouvrir **pgAdmin**
2. Se connecter au serveur PostgreSQL
3. Créer la base de données `patronymes_db`
4. Utiliser l'outil **Query Tool**
5. Exécuter les scripts SQL

#### **3.2. Scripts SQL à exécuter**

```sql
-- Créer les tables (migrations Laravel)
-- Puis insérer les données (seeders Laravel)
```

## 🚀 **Chargement Complet des Données**

### **Étape 1 : Données Administratives**

```bash
# Données du Burkina Faso (13 régions, 45 provinces, 95 communes)
php artisan db:seed --class=BurkinaFasoDataSeeder
```

### **Étape 2 : Données de Base**

```bash
# Groupes ethniques, langues, modes de transmission
php artisan db:seed --class=PatronymesSeeder
```

### **Étape 3 : Patronymes Réels**

```bash
# 17 patronymes authentiques du Burkina Faso
php artisan db:seed --class=RealPatronymesSeeder
```

### **Étape 4 : Utilisateurs de Test**

```bash
# Utilisateurs avec différents rôles
php artisan db:seed --class=UsersSeeder
```

## 📋 **Structure des Données Chargées**

### **Tables Principales**

```sql
-- Régions (13)
SELECT nom, code FROM regions ORDER BY nom;

-- Provinces (45)
SELECT nom, region_id FROM provinces ORDER BY nom;

-- Communes (95)
SELECT nom, province_id FROM communes ORDER BY nom;

-- Patronymes (17 uniques)
SELECT nom, signification, views_count FROM patronymes ORDER BY views_count DESC;
```

### **Patronymes Disponibles**

```
1. Traoré (2100 vues) - Empire du Mali
2. Sawadogo (1800 vues) - Rois de Ouagadougou
3. Kabré (1450 vues) - Guerriers Gourmantché
4. Ouédraogo (1250 vues) - Rois du Yatenga
5. Zongo (1200 vues) - Chefs de guerre Mossi
6. Tankoano (1100 vues) - Rois de Tenkodogo
7. Kaboré (950 vues) - Chefs spirituels Mossi
8. Bationo (850 vues) - Chefs de la pluie
9. Bamba (780 vues) - Nobles Mandé
10. Ouattara (720 vues) - Chefs de guerre Mandé
11. Zerbo (680 vues) - Chefs de la terre
12. Compaoré (650 vues) - Chefs de guerre Mossi
13. Kinda (580 vues) - Chefs spirituels Gourmantché
14. Boro (520 vues) - Chefs Bissa
15. Yaméogo (480 vues) - Chefs de guerre Mossi
16. Bado (420 vues) - Chefs de la terre Gourmantché
17. Ouedraogo (1150 vues) - Variante Ouédraogo
```

## 🔍 **Vérification des Données**

### **Requêtes de Vérification**

```sql
-- Compter les données
SELECT 'Régions' as table_name, COUNT(*) as count FROM regions
UNION ALL
SELECT 'Provinces', COUNT(*) FROM provinces
UNION ALL
SELECT 'Communes', COUNT(*) FROM communes
UNION ALL
SELECT 'Patronymes', COUNT(*) FROM patronymes
UNION ALL
SELECT 'Utilisateurs', COUNT(*) FROM users;

-- Vérifier les relations
SELECT
    p.nom,
    r.nom as region,
    pr.nom as province,
    c.nom as commune
FROM patronymes p
LEFT JOIN regions r ON p.region_id = r.id
LEFT JOIN provinces pr ON p.province_id = pr.id
LEFT JOIN communes c ON p.commune_id = c.id
ORDER BY p.views_count DESC;
```

### **Commandes Laravel**

```bash
# Vérifier la base de données
php artisan migrate:status

# Vérifier les seeders
php artisan db:seed --class=BurkinaFasoDataSeeder --dry-run

# Compter les données
php artisan tinker --execute="
echo 'Régions: ' . App\Models\Region::count() . PHP_EOL;
echo 'Provinces: ' . App\Models\Province::count() . PHP_EOL;
echo 'Communes: ' . App\Models\Commune::count() . PHP_EOL;
echo 'Patronymes: ' . App\Models\Patronyme::count() . PHP_EOL;
echo 'Utilisateurs: ' . App\Models\User::count() . PHP_EOL;
"
```

## 🛡️ **Sécurité et Sauvegarde**

### **Sauvegarde PostgreSQL**

```bash
# Sauvegarde complète
pg_dump -U postgres -h localhost -d patronymes_db > patronymes_backup_$(date +%Y%m%d).sql

# Sauvegarde des données uniquement
pg_dump -U postgres -h localhost -d patronymes_db --data-only > patronymes_data_$(date +%Y%m%d).sql

# Sauvegarde de la structure uniquement
pg_dump -U postgres -h localhost -d patronymes_db --schema-only > patronymes_schema_$(date +%Y%m%d).sql
```

### **Restauration**

```bash
# Restaurer depuis une sauvegarde
psql -U postgres -d patronymes_db -f patronymes_backup_20250107.sql
```

## 🎯 **Optimisations PostgreSQL**

### **Index Créés**

```sql
-- Index de recherche insensibles à la casse
CREATE INDEX idx_patronymes_nom_lower ON patronymes (lower(nom));
CREATE INDEX idx_patronymes_signification_lower ON patronymes (lower(signification));

-- Index de recherche plein texte
CREATE INDEX idx_patronymes_nom_fts ON patronymes USING gin(to_tsvector('french', nom));
CREATE INDEX idx_patronymes_signification_fts ON patronymes USING gin(to_tsvector('french', signification));

-- Contrainte d'unicité
ALTER TABLE patronymes ADD CONSTRAINT patronymes_nom_unique UNIQUE (nom);
```

### **Configuration PostgreSQL Recommandée**

```sql
-- Optimiser pour la recherche
ALTER SYSTEM SET shared_preload_libraries = 'pg_stat_statements';
ALTER SYSTEM SET max_connections = 200;
ALTER SYSTEM SET shared_buffers = '256MB';
ALTER SYSTEM SET effective_cache_size = '1GB';
ALTER SYSTEM SET random_page_cost = 1.1;
ALTER SYSTEM SET effective_io_concurrency = 200;

-- Redémarrer PostgreSQL après ces modifications
```

## 🚀 **Commandes Rapides**

### **Chargement Complet**

```bash
# Tout recharger depuis le début
php artisan migrate:fresh --seed
```

### **Chargement Partiel**

```bash
# Seulement les données administratives
php artisan db:seed --class=BurkinaFasoDataSeeder

# Seulement les patronymes
php artisan db:seed --class=RealPatronymesSeeder

# Seulement les utilisateurs
php artisan db:seed --class=UsersSeeder
```

### **Vérification Rapide**

```bash
# Compter tout
php artisan tinker --execute="echo 'Total: ' . App\Models\Patronyme::count();"

# Tester la recherche
php artisan tinker --execute="echo 'Traoré: ' . App\Models\Patronyme::whereRaw('LOWER(nom) LIKE ?', ['%traore%'])->count();"
```

---

**🎉 Base de données PostgreSQL optimisée avec 17 patronymes uniques et sans doublons !**

**Données prêtes pour la production avec contraintes d'unicité et index optimisés !** 🚀
