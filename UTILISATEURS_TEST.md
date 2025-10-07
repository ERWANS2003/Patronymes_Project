# 👥 Utilisateurs de Test - Patronymes Project

## 📋 Liste des utilisateurs créés

Le projet contient maintenant **10 utilisateurs de test** avec différents rôles et permissions pour tester toutes les fonctionnalités de la plateforme.

## 🔐 Identifiants de connexion

### 👑 **Administrateurs**

| Email                      | Mot de passe | Rôle         | Permissions        |
| -------------------------- | ------------ | ------------ | ------------------ |
| `admin@patronymes.bf`      | `password`   | Admin        | Toutes permissions |
| `moderateur@patronymes.bf` | `password`   | Contributeur | Gestion des rôles  |

### 👨‍💼 **Contributeurs Experts**

| Email                        | Mot de passe | Rôle         | Permissions  |
| ---------------------------- | ------------ | ------------ | ------------ |
| `contributeur@patronymes.bf` | `password`   | Contributeur | Contribution |
| `chercheur@patronymes.bf`    | `password`   | Contributeur | Contribution |
| `archiviste@patronymes.bf`   | `password`   | Contributeur | Contribution |

### 👤 **Utilisateurs Réguliers**

| Email                       | Mot de passe | Rôle | Permissions   |
| --------------------------- | ------------ | ---- | ------------- |
| `user@patronymes.bf`        | `password`   | User | Lecture seule |
| `etudiant@patronymes.bf`    | `password`   | User | Contribution  |
| `journaliste@patronymes.bf` | `password`   | User | Contribution  |
| `test@patronymes.bf`        | `password`   | User | Lecture seule |

### ❌ **Utilisateur Inactif**

| Email                   | Mot de passe | Rôle | Statut  |
| ----------------------- | ------------ | ---- | ------- |
| `inactif@patronymes.bf` | `password`   | User | Inactif |

## 🎯 Profils détaillés

### 1. **Administrateur Principal**

-   **Nom** : Administrateur Principal
-   **Email** : admin@patronymes.bf
-   **Rôle** : Admin
-   **Statut** : Actif
-   **Connexions** : 25
-   **Dernière connexion** : Il y a 2h
-   **Permissions** : Toutes (gestion, contribution, rôles)

### 2. **Dr. Mamadou Sawadogo**

-   **Nom** : Dr. Mamadou Sawadogo
-   **Email** : contributeur@patronymes.bf
-   **Rôle** : Contributeur
-   **Statut** : Actif
-   **Connexions** : 18
-   **Dernière connexion** : Il y a 5h
-   **Permissions** : Contribution

### 3. **Fatoumata Traoré**

-   **Nom** : Fatoumata Traoré
-   **Email** : user@patronymes.bf
-   **Rôle** : User
-   **Statut** : Actif
-   **Connexions** : 12
-   **Dernière connexion** : Il y a 1h
-   **Permissions** : Lecture seule

### 4. **Pr. Ibrahim Ouédraogo**

-   **Nom** : Pr. Ibrahim Ouédraogo
-   **Email** : chercheur@patronymes.bf
-   **Rôle** : Contributeur
-   **Statut** : Actif
-   **Connexions** : 35
-   **Dernière connexion** : Il y a 30 min
-   **Permissions** : Contribution

### 5. **Aïcha Kaboré**

-   **Nom** : Aïcha Kaboré
-   **Email** : etudiant@patronymes.bf
-   **Rôle** : User
-   **Statut** : Actif
-   **Connexions** : 8
-   **Dernière connexion** : Il y a 1 jour
-   **Permissions** : Contribution

### 6. **Boubacar Zongo**

-   **Nom** : Boubacar Zongo
-   **Email** : archiviste@patronymes.bf
-   **Rôle** : Contributeur
-   **Statut** : Actif
-   **Connexions** : 42
-   **Dernière connexion** : Il y a 12h
-   **Permissions** : Contribution

### 7. **Mariam Compaoré**

-   **Nom** : Mariam Compaoré
-   **Email** : journaliste@patronymes.bf
-   **Rôle** : User
-   **Statut** : Actif
-   **Connexions** : 15
-   **Dernière connexion** : Il y a 2 jours
-   **Permissions** : Contribution

### 8. **Seydou Koné**

-   **Nom** : Seydou Koné
-   **Email** : inactif@patronymes.bf
-   **Rôle** : User
-   **Statut** : Inactif
-   **Connexions** : 3
-   **Dernière connexion** : Il y a 2 mois
-   **Permissions** : Aucune

### 9. **Halima Sangaré**

-   **Nom** : Halima Sangaré
-   **Email** : moderateur@patronymes.bf
-   **Rôle** : Contributeur
-   **Statut** : Actif
-   **Connexions** : 28
-   **Dernière connexion** : Il y a 6h
-   **Permissions** : Gestion des rôles

### 10. **Test User**

-   **Nom** : Test User
-   **Email** : test@patronymes.bf
-   **Rôle** : User
-   **Statut** : Actif
-   **Connexions** : 1
-   **Dernière connexion** : Il y a 10 min
-   **Permissions** : Lecture seule

## 🚀 Comment utiliser ces utilisateurs

### Connexion à l'application

1. Accédez à http://localhost:8000/login
2. Utilisez l'un des emails ci-dessus
3. Mot de passe : `password`

### Test des permissions

#### 👑 **Administrateur**

-   Accès complet à toutes les fonctionnalités
-   Gestion des utilisateurs et des rôles
-   Modération du contenu
-   Statistiques et rapports

#### 👨‍💼 **Contributeurs**

-   Création et modification des patronymes
-   Gestion des contributions
-   Modération des commentaires (selon les permissions)

#### 👤 **Utilisateurs**

-   Consultation des patronymes
-   Recherche et filtres
-   Ajout de commentaires
-   Favoris (selon les permissions)

## 🔧 Statistiques des utilisateurs

-   **Total** : 10 utilisateurs
-   **Administrateurs** : 1
-   **Contributeurs** : 4
-   **Utilisateurs** : 5
-   **Actifs** : 9
-   **Inactifs** : 1

## 📝 Notes importantes

-   Tous les mots de passe sont identiques : `password`
-   Les utilisateurs sont créés avec des données réalistes
-   Les statistiques de connexion sont simulées
-   Les emails de vérification sont marqués comme vérifiés
-   Les utilisateurs inactifs ne peuvent pas se connecter

## 🎯 Cas d'usage recommandés

1. **Test complet** : Utiliser `admin@patronymes.bf`
2. **Test contribution** : Utiliser `contributeur@patronymes.bf`
3. **Test utilisateur** : Utiliser `user@patronymes.bf`
4. **Test recherche** : Utiliser `chercheur@patronymes.bf`
5. **Test simple** : Utiliser `test@patronymes.bf`

---

**Ces utilisateurs permettent de tester tous les aspects de la plateforme Patronymes Project !** 🇧🇫
