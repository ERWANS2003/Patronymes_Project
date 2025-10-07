#!/bin/bash

# Script de chargement des données dans PostgreSQL
# Usage: ./load_postgresql_data.sh

echo "🗄️  Chargement des données dans PostgreSQL"
echo "=========================================="

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonction pour afficher les messages colorés
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_step() {
    echo -e "${BLUE}[STEP]${NC} $1"
}

# Vérifier si nous sommes dans le bon répertoire
if [ ! -f "artisan" ]; then
    print_error "Ce script doit être exécuté depuis le répertoire racine de Laravel"
    exit 1
fi

# Vérifier si PostgreSQL est accessible
print_step "Vérification de la connexion PostgreSQL..."
if ! php artisan tinker --execute="echo 'Test connexion: ' . DB::connection()->getDatabaseName();" > /dev/null 2>&1; then
    print_error "Impossible de se connecter à PostgreSQL. Vérifiez votre configuration .env"
    exit 1
fi
print_status "Connexion PostgreSQL OK"

# Sauvegarde de sécurité
print_step "Création d'une sauvegarde de sécurité..."
BACKUP_FILE="backup_before_load_$(date +%Y%m%d_%H%M%S).sql"
if command -v pg_dump > /dev/null; then
    DB_NAME=$(php artisan tinker --execute="echo config('database.connections.pgsql.database');" 2>/dev/null)
    pg_dump -U postgres -h localhost -d $DB_NAME > $BACKUP_FILE 2>/dev/null
    if [ $? -eq 0 ]; then
        print_status "Sauvegarde créée: $BACKUP_FILE"
    else
        print_warning "Impossible de créer la sauvegarde automatique"
    fi
else
    print_warning "pg_dump non trouvé, pas de sauvegarde automatique"
fi

# Nettoyage des données existantes
print_step "Nettoyage des données existantes..."
php artisan migrate:fresh --force
if [ $? -eq 0 ]; then
    print_status "Base de données nettoyée"
else
    print_error "Erreur lors du nettoyage de la base de données"
    exit 1
fi

# Chargement des données administratives du Burkina Faso
print_step "Chargement des données administratives du Burkina Faso..."
php artisan db:seed --class=BurkinaFasoDataSeeder
if [ $? -eq 0 ]; then
    print_status "✅ Données administratives chargées (13 régions, 45 provinces, 95 communes)"
else
    print_error "Erreur lors du chargement des données administratives"
    exit 1
fi

# Chargement des données de base
print_step "Chargement des données de base..."
php artisan db:seed --class=PatronymesSeeder
if [ $? -eq 0 ]; then
    print_status "✅ Données de base chargées (groupes ethniques, langues, etc.)"
else
    print_error "Erreur lors du chargement des données de base"
    exit 1
fi

# Chargement des patronymes réels
print_step "Chargement des patronymes réels du Burkina Faso..."
php artisan db:seed --class=RealPatronymesSeeder
if [ $? -eq 0 ]; then
    print_status "✅ Patronymes réels chargés"
else
    print_error "Erreur lors du chargement des patronymes réels"
    exit 1
fi

# Nettoyage des doublons
print_step "Nettoyage des doublons..."
php artisan patronymes:clean-duplicates
if [ $? -eq 0 ]; then
    print_status "✅ Doublons supprimés"
else
    print_warning "Erreur lors du nettoyage des doublons (peut être normal s'il n'y en a pas)"
fi

# Chargement des utilisateurs de test
print_step "Chargement des utilisateurs de test..."
php artisan db:seed --class=UsersSeeder
if [ $? -eq 0 ]; then
    print_status "✅ Utilisateurs de test chargés"
else
    print_error "Erreur lors du chargement des utilisisateurs"
    exit 1
fi

# Vérification finale
print_step "Vérification finale des données..."
REGIONS=$(php artisan tinker --execute="echo App\Models\Region::count();" 2>/dev/null)
PROVINCES=$(php artisan tinker --execute="echo App\Models\Province::count();" 2>/dev/null)
COMMUNES=$(php artisan tinker --execute="echo App\Models\Commune::count();" 2>/dev/null)
PATRONYMES=$(php artisan tinker --execute="echo App\Models\Patronyme::count();" 2>/dev/null)
USERS=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null)

print_status "📊 Résultats du chargement:"
echo "   - Régions: $REGIONS"
echo "   - Provinces: $PROVINCES"
echo "   - Communes: $COMMUNES"
echo "   - Patronymes: $PATRONYMES"
echo "   - Utilisateurs: $USERS"

# Vérification de la cohérence
if [ "$REGIONS" -eq 13 ] && [ "$PROVINCES" -eq 45 ] && [ "$COMMUNES" -eq 95 ] && [ "$PATRONYMES" -gt 0 ] && [ "$USERS" -gt 0 ]; then
    print_status "✅ Toutes les données ont été chargées correctement !"
else
    print_warning "⚠️  Certaines données peuvent être manquantes"
fi

# Nettoyage du cache
print_step "Nettoyage du cache..."
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
print_status "Cache nettoyé"

# Génération des identifiants de connexion
print_step "Génération des identifiants de connexion..."
echo ""
echo "🔐 Identifiants de connexion:"
echo "============================="
echo "Admin: admin@patronymes.bf / password"
echo "Contributeur: contributeur@patronymes.bf / password"
echo "Utilisateur: user@patronymes.bf / password"
echo "Chercheur: chercheur@patronymes.bf / password"
echo "Étudiant: etudiant@patronymes.bf / password"
echo ""

# URL de test
echo "🌐 URLs de test:"
echo "================"
echo "Application: http://localhost:8000"
echo "Recherche: http://localhost:8000/patronymes"
echo "Admin: http://localhost:8000/admin"
echo "Recherche avancée: http://localhost:8000/patronymes/search/advanced"
echo ""

print_status "🎉 Chargement terminé avec succès !"
print_status "Vous pouvez maintenant démarrer l'application avec: php artisan serve"

exit 0
