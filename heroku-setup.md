# 🚀 Déploiement Heroku - Instructions

## 📋 Étapes de Déploiement

### 1. **Préparation**
```bash
# Redémarrer le terminal pour que Heroku CLI soit reconnu
# Puis exécuter :
heroku login
```

### 2. **Création de l'Application**
```bash
heroku create patronymes-app
```

### 3. **Configuration de la Base de Données**
```bash
heroku addons:create heroku-postgresql:mini
```

### 4. **Configuration des Variables d'Environnement**
```bash
heroku config:set APP_KEY=$(php artisan key:generate --show)
heroku config:set APP_ENV=production
heroku config:set APP_DEBUG=false
```

### 5. **Déploiement**
```bash
git push heroku main
```

### 6. **Migration et Seeding**
```bash
heroku run php artisan migrate --force
heroku run php artisan db:seed --force
```

### 7. **Ouverture de l'Application**
```bash
heroku open
```

## 🌐 URL de l'Application
Une fois déployée, votre application sera accessible sur :
**https://patronymes-app.herokuapp.com**

## 📱 Accès Mobile
L'application sera accessible depuis n'importe quel appareil via l'URL Heroku.

## 🔐 Comptes de Test
- Créez un compte admin via l'interface
- Email : admin@patronymes.bf
- Mot de passe : password123
