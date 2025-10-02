# 🚀 Déploiement Railway - Instructions Simples

## 📋 Étapes de Déploiement

### 1. **Aller sur Railway**

-   Visitez : https://railway.app
-   Cliquez sur "Login" et connectez-vous avec GitHub

### 2. **Créer un Nouveau Projet**

-   Cliquez sur "New Project"
-   Sélectionnez "Deploy from GitHub repo"
-   Choisissez votre repository `patronymes-app`

### 3. **Configuration Automatique**

Railway détectera automatiquement que c'est une application Laravel et configurera :

-   PHP 8.2
-   PostgreSQL
-   Variables d'environnement

### 4. **Variables d'Environnement**

Railway configurera automatiquement :

```
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=pgsql
```

### 5. **Déploiement Automatique**

-   Railway déploiera automatiquement votre application
-   Les migrations s'exécuteront automatiquement
-   Les seeders se lanceront automatiquement

### 6. **URL de l'Application**

Une fois déployée, vous obtiendrez une URL comme :
**https://patronymes-app-production.up.railway.app**

## 🌐 Avantages de Railway

✅ **Déploiement automatique** depuis GitHub  
✅ **Base de données PostgreSQL** incluse  
✅ **Variables d'environnement** automatiques  
✅ **SSL/HTTPS** automatique  
✅ **Mise à jour automatique** à chaque push  
✅ **Gratuit** pour les petits projets

## 📱 Accès Mobile

L'application sera accessible depuis n'importe quel appareil via l'URL Railway.

## 🔐 Comptes de Test

-   Créez un compte admin via l'interface
-   Email : admin@patronymes.bf
-   Mot de passe : password123
