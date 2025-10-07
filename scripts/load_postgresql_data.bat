@echo off
REM Script de chargement des données dans PostgreSQL pour Windows
REM Usage: load_postgresql_data.bat

echo 🗄️  Chargement des données dans PostgreSQL
echo ==========================================

REM Vérifier si nous sommes dans le bon répertoire
if not exist "artisan" (
    echo [ERROR] Ce script doit être exécuté depuis le répertoire racine de Laravel
    pause
    exit /b 1
)

REM Vérifier si PostgreSQL est accessible
echo [STEP] Vérification de la connexion PostgreSQL...
php artisan tinker --execute="echo 'Test connexion: ' . DB::connection()->getDatabaseName();" >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Impossible de se connecter à PostgreSQL. Vérifiez votre configuration .env
    pause
    exit /b 1
)
echo [INFO] Connexion PostgreSQL OK

REM Nettoyage des données existantes
echo [STEP] Nettoyage des données existantes...
php artisan migrate:fresh --force
if errorlevel 1 (
    echo [ERROR] Erreur lors du nettoyage de la base de données
    pause
    exit /b 1
)
echo [INFO] Base de données nettoyée

REM Chargement des données administratives du Burkina Faso
echo [STEP] Chargement des données administratives du Burkina Faso...
php artisan db:seed --class=BurkinaFasoDataSeeder
if errorlevel 1 (
    echo [ERROR] Erreur lors du chargement des données administratives
    pause
    exit /b 1
)
echo [INFO] ✅ Données administratives chargées (13 régions, 45 provinces, 95 communes)

REM Chargement des données de base
echo [STEP] Chargement des données de base...
php artisan db:seed --class=PatronymesSeeder
if errorlevel 1 (
    echo [ERROR] Erreur lors du chargement des données de base
    pause
    exit /b 1
)
echo [INFO] ✅ Données de base chargées (groupes ethniques, langues, etc.)

REM Chargement des patronymes réels
echo [STEP] Chargement des patronymes réels du Burkina Faso...
php artisan db:seed --class=RealPatronymesSeeder
if errorlevel 1 (
    echo [ERROR] Erreur lors du chargement des patronymes réels
    pause
    exit /b 1
)
echo [INFO] ✅ Patronymes réels chargés

REM Nettoyage des doublons
echo [STEP] Nettoyage des doublons...
php artisan patronymes:clean-duplicates
if errorlevel 1 (
    echo [WARN] Erreur lors du nettoyage des doublons (peut être normal s'il n'y en a pas)
) else (
    echo [INFO] ✅ Doublons supprimés
)

REM Chargement des utilisateurs de test
echo [STEP] Chargement des utilisateurs de test...
php artisan db:seed --class=UsersSeeder
if errorlevel 1 (
    echo [ERROR] Erreur lors du chargement des utilisateurs
    pause
    exit /b 1
)
echo [INFO] ✅ Utilisateurs de test chargés

REM Vérification finale
echo [STEP] Vérification finale des données...
for /f %%i in ('php artisan tinker --execute="echo App\Models\Region::count();" 2^>nul') do set REGIONS=%%i
for /f %%i in ('php artisan tinker --execute="echo App\Models\Province::count();" 2^>nul') do set PROVINCES=%%i
for /f %%i in ('php artisan tinker --execute="echo App\Models\Commune::count();" 2^>nul') do set COMMUNES=%%i
for /f %%i in ('php artisan tinker --execute="echo App\Models\Patronyme::count();" 2^>nul') do set PATRONYMES=%%i
for /f %%i in ('php artisan tinker --execute="echo App\Models\User::count();" 2^>nul') do set USERS=%%i

echo [INFO] 📊 Résultats du chargement:
echo    - Régions: %REGIONS%
echo    - Provinces: %PROVINCES%
echo    - Communes: %COMMUNES%
echo    - Patronymes: %PATRONYMES%
echo    - Utilisateurs: %USERS%

REM Vérification de la cohérence
if %REGIONS%==13 if %PROVINCES%==45 if %COMMUNES%==95 if %PATRONYMES% GTR 0 if %USERS% GTR 0 (
    echo [INFO] ✅ Toutes les données ont été chargées correctement !
) else (
    echo [WARN] ⚠️  Certaines données peuvent être manquantes
)

REM Nettoyage du cache
echo [STEP] Nettoyage du cache...
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
echo [INFO] Cache nettoyé

REM Génération des identifiants de connexion
echo [STEP] Génération des identifiants de connexion...
echo.
echo 🔐 Identifiants de connexion:
echo =============================
echo Admin: admin@patronymes.bf / password
echo Contributeur: contributeur@patronymes.bf / password
echo Utilisateur: user@patronymes.bf / password
echo Chercheur: chercheur@patronymes.bf / password
echo Étudiant: etudiant@patronymes.bf / password
echo.

REM URL de test
echo 🌐 URLs de test:
echo ================
echo Application: http://localhost:8000
echo Recherche: http://localhost:8000/patronymes
echo Admin: http://localhost:8000/admin
echo Recherche avancée: http://localhost:8000/patronymes/search/advanced
echo.

echo [INFO] 🎉 Chargement terminé avec succès !
echo [INFO] Vous pouvez maintenant démarrer l'application avec: php artisan serve

pause
exit /b 0
