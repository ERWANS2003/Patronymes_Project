# 🏛️ Répertoire des Patronymes du Burkina Faso

Une application web complète pour explorer, rechercher et gérer les patronymes du Burkina Faso avec une interface moderne et des fonctionnalités avancées.

## 🌟 Fonctionnalités Principales

### 🔍 **Recherche et Exploration**
- **Recherche intelligente** par nom, origine ou signification
- **Filtres avancés** : région, province, commune, groupe ethnique, ethnie, langue
- **Sélecteurs dynamiques** : région → province → commune
- **Pagination** des résultats

### 👥 **Gestion des Utilisateurs**
- **Authentification complète** (Jetstream + Fortify)
- **Système de rôles** (Admin/Utilisateur)
- **Profils utilisateurs** avec informations détaillées
- **Système de favoris** pour sauvegarder les patronymes préférés

### 📊 **Tableaux de Bord**
- **Statistiques visuelles** avec graphiques interactifs
- **Analyses par région, groupe ethnique, langue**
- **Patronymes les plus consultés**
- **Métriques d'utilisation**

### 🛠️ **Administration**
- **CRUD complet** pour les patronymes
- **Import/Export Excel** des données
- **Gestion des utilisateurs**
- **Interface d'administration sécurisée**

### 🔌 **API REST**
- **Endpoints complets** pour toutes les fonctionnalités
- **Documentation Swagger** intégrée
- **Authentification par tokens**
- **Format JSON standardisé**

## 🚀 Installation

### Prérequis
- PHP 8.1+
- Composer
- Node.js & NPM
- Base de données (PostgreSQL/MySQL/SQLite)

### Étapes d'installation

1. **Cloner le projet**
```bash
git clone https://github.com/ERWANS2003/Patronymes_Project.git
cd Patronymes_Project
```

2. **Installer les dépendances**
```bash
composer install
npm install
```

3. **Configuration de l'environnement**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configuration de la base de données**
```env
DB_CONNECTION=postgresql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=patronymes_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Exécuter les migrations et seeders**
```bash
php artisan migrate --seed
```

6. **Compiler les assets**
```bash
npm run build
```

7. **Démarrer le serveur**
```bash
php artisan serve
```

## 👤 Comptes de Test

### Administrateur
- **Email :** `admin@patronymes.bf`
- **Mot de passe :** `password`
- **Accès :** Toutes les fonctionnalités + Administration

### Utilisateur Standard
- Créez un compte via l'interface d'inscription
- Accès aux fonctionnalités de base + favoris

## 📱 Interface Utilisateur

### Page d'Accueil
- **Hero section** avec recherche rapide
- **Présentation des fonctionnalités**
- **Statistiques en temps réel**

### Liste des Patronymes
- **Tableau interactif** avec tri et filtres
- **Recherche en temps réel**
- **Actions rapides** (voir, modifier, supprimer)

### Détails d'un Patronyme
- **Informations complètes** (origine, signification, histoire)
- **Bouton favori** pour les utilisateurs connectés
- **Compteur de vues**
- **Navigation contextuelle**

### Administration
- **Dashboard** avec métriques
- **Gestion des patronymes** (CRUD)
- **Import/Export** de données
- **Statistiques avancées**

## 🧪 Tests

```bash
# Exécuter tous les tests
php artisan test

# Tests spécifiques
php artisan test --filter=PatronymesFilterTest
php artisan test --filter=AdminRoutesTest
```

## 📊 Données Incluses

### Géographie
- **13 régions** du Burkina Faso
- **45 provinces** avec communes
- **Données géographiques** complètes

### Ethnies et Langues
- **Groupes ethniques** principaux
- **Langues locales** avec modes de transmission
- **Relations ethnies ↔ groupes**

### Patronymes
- **7 patronymes** d'exemple
- **Données réalistes** du Burkina Faso
- **Relations géographiques** et culturelles

## 🔧 Technologies Utilisées

### Backend
- **Laravel 12** - Framework PHP
- **Jetstream** - Authentification
- **Livewire 3** - Composants réactifs
- **Sanctum** - API tokens
- **Maatwebsite/Excel** - Import/Export

### Frontend
- **Bootstrap 5** - Interface responsive
- **Alpine.js** - Interactivité
- **Chart.js** - Graphiques
- **Font Awesome** - Icônes
- **Animate.css** - Animations

### Base de Données
- **PostgreSQL** (production)
- **SQLite** (tests)
- **Eloquent ORM** - Relations avancées

## 📈 Fonctionnalités Avancées

### Sécurité
- **Rate limiting** sur les API
- **Headers de sécurité** automatiques
- **Validation CSRF** complète
- **Sanitisation** des entrées

### Performance
- **Pagination** optimisée
- **Relations Eloquent** chargées efficacement
- **Cache** des requêtes fréquentes
- **Assets** optimisés

### UX/UI
- **Design responsive** mobile-first
- **Animations** fluides
- **Feedback** utilisateur en temps réel
- **Navigation** intuitive

## 🚀 Déploiement

### Production
1. **Serveur web** (Nginx/Apache)
2. **Base de données** PostgreSQL
3. **Cache** Redis (optionnel)
4. **SSL** obligatoire
5. **Variables d'environnement** sécurisées

### Variables d'environnement importantes
```env
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=postgresql
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

## 📝 API Documentation

L'API est documentée avec Swagger/OpenAPI :
- **URL :** `/docs`
- **Format :** JSON
- **Authentification :** Bearer Token

### Endpoints principaux
- `GET /api/patronymes` - Liste des patronymes
- `POST /api/patronymes` - Créer un patronyme
- `GET /api/patronymes/{id}` - Détails d'un patronyme
- `GET /api/regions` - Liste des régions
- `POST /api/auth/login` - Connexion

## 🤝 Contribution

1. **Fork** le projet
2. **Créer** une branche feature
3. **Commit** vos changements
4. **Push** vers la branche
5. **Ouvrir** une Pull Request

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 👨‍💻 Auteur

**Erwan** - [GitHub](https://github.com/ERWANS2003)

## 📞 Support

Pour toute question ou problème :
- **Issues GitHub** : [Créer une issue](https://github.com/ERWANS2003/Patronymes_Project/issues)
- **Email** : support@patronymes.bf

---

**🎯 Objectif :** Préserver et partager le patrimoine patronymique du Burkina Faso à travers une plateforme moderne et accessible.

**🌟 Star** ce projet si vous l'appréciez !