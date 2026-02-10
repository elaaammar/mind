# MindAudit - Plateforme d'Audit Interne Intelligente

## 📋 Description du projet

MindAudit est une plateforme d'audit interne intelligente qui automatise les audits internes d'entreprises. Le système utilise l'IA pour poser les questions pertinentes, collecter les documents nécessaires, analyser les données et générer automatiquement des rapports d'audit détaillés.

## 🎯 Fonctionnalités principales

### Module de Gestion des Utilisateurs (Implémenté)

#### Fonctionnalités CRUD
- ✅ **Créer** un utilisateur avec validation côté serveur
- ✅ **Lire** la liste des utilisateurs avec recherche et tri
- ✅ **Modifier** les informations d'un utilisateur
- ✅ **Supprimer** un utilisateur avec confirmation

#### Fonctionnalités avancées
- 🔍 **Recherche** par nom, prénom ou email
- 📊 **Tri** par nom, prénom, email ou date de création
- 🎯 **Filtrage** par rôle
- 🔐 **Hash automatique** des mots de passe
- ✅ **Activation/Désactivation** des comptes
- 📧 **Validation email** unique
- 🔒 **Contrôles de saisie** côté serveur (pas de HTML/JS)

### Module de Gestion des Rôles (Implémenté)

#### Fonctionnalités CRUD
- ✅ **Créer** un rôle avec permissions
- ✅ **Lire** la liste des rôles
- ✅ **Modifier** un rôle et ses permissions
- ✅ **Supprimer** un rôle (avec vérification)

#### Fonctionnalités avancées
- 🔍 **Recherche** par nom ou description
- 🔑 **Gestion des permissions** (checkboxes multiples)
- 👥 **Affichage** des utilisateurs par rôle
- ⚠️ **Protection** contre la suppression de rôles assignés

## 🏗️ Architecture

### Architecture MVC (Symfony)
```
MindAudit/
├── src/
│   ├── Controller/          # Contrôleurs (HomeController, UtilisateurController, RoleController)
│   ├── Entity/              # Entités (Utilisateur, Role)
│   ├── Form/                # Formulaires (UtilisateurType, RoleType)
│   ├── Repository/          # Repositories avec méthodes de recherche
│   └── DataFixtures/        # Données de test
├── templates/
│   ├── base.html.twig       # Template de base avec Bootstrap 5
│   ├── home/                # Pages d'accueil et dashboard
│   ├── utilisateur/         # Templates utilisateurs
│   └── role/                # Templates rôles
├── public/                  # Fichiers publics
└── config/                  # Configuration Symfony
```

### Base de données (MySQL via XAMPP)

#### Entité Utilisateur
- `id` : Identifiant unique
- `nom` : Nom de famille (min 2 caractères)
- `prenom` : Prénom (min 2 caractères)
- `email` : Email unique et valide
- `password` : Mot de passe hashé (min 6 caractères)
- `actif` : Statut du compte (boolean)
- `createdAt` : Date de création
- `role_id` : Clé étrangère vers Role (ManyToOne)

#### Entité Role
- `id` : Identifiant unique
- `nom` : Nom du rôle (unique, min 3 caractères)
- `description` : Description du rôle
- `permissions` : Tableau JSON des permissions
- `createdAt` : Date de création
- Relation OneToMany avec Utilisateur

#### Relation
- **ManyToOne** : Un Utilisateur → Un Role
- **OneToMany** : Un Role → Plusieurs Utilisateurs

## 🚀 Installation

### Prérequis
- PHP 8.1+
- Composer
- XAMPP (MySQL)
- Symfony CLI

### Étapes d'installation

1. **Démarrer XAMPP**
   - Ouvrez XAMPP Control Panel
   - Démarrez Apache et MySQL

2. **Installer les dépendances**
   ```bash
   cd MindAudit
   composer install
   ```

3. **Configurer la base de données**
   - Le fichier `.env` est déjà configuré pour XAMPP :
   ```
   DATABASE_URL="mysql://root:@127.0.0.1:3306/mindaudit?serverVersion=8.0.32&charset=utf8mb4"
   ```

4. **Créer la base de données**
   ```bash
   php bin/console doctrine:database:create
   ```

5. **Créer et exécuter les migrations**
   ```bash
   php bin/console make:migration
   php bin/console doctrine:migrations:migrate
   ```

6. **Charger les données de test (optionnel)**
   ```bash
   composer require --dev orm-fixtures
   php bin/console doctrine:fixtures:load
   ```

7. **Démarrer le serveur**
   ```bash
   symfony server:start
   ```
   OU
   ```bash
   php -S localhost:8000 -t public
   ```

8. **Accéder à l'application**
   - Page d'accueil : http://localhost:8000/
   - Dashboard : http://localhost:8000/dashboard
   - Utilisateurs : http://localhost:8000/utilisateur
   - Rôles : http://localhost:8000/role

## 👤 Comptes de test (après fixtures)

| Email | Mot de passe | Rôle |
|-------|--------------|------|
| admin@mindaudit.com | admin123 | Administrateur |
| jean.dupont@mindaudit.com | password123 | Auditeur |
| sophie.martin@mindaudit.com | password123 | Auditeur |
| pierre.bernard@mindaudit.com | password123 | Utilisateur |
| marie.leroy@mindaudit.com | password123 | Utilisateur |

## 🎭 Les 3 Rôles du système

### 1. **Administrateur**
- Accès complet au système
- Gestion des utilisateurs (créer, modifier, supprimer)
- Gestion des rôles et permissions
- Gestion des audits
- Configuration du système

### 2. **Auditeur**
- Responsable des audits internes
- Créer et gérer les audits
- Générer des rapports d'audit
- Consulter les utilisateurs et rôles
- Analyser les données d'audit

### 3. **Utilisateur**
- Utilisateur standard de l'organisation
- Consulter les audits
- Participer aux processus d'audit
- Accès en lecture aux informations

## 🎨 Interface utilisateur

### Front Office & Back Office
- **Template Bootstrap 5** responsive
- **Sidebar** de navigation
- **Navbar** avec liens fonctionnels
- **Messages flash** pour les notifications
- **Icônes Font Awesome**
- **Design moderne** avec cartes et badges
- **Modals** pour les confirmations

### Pages implémentées
1. **Page d'accueil** - Présentation de MindAudit
2. **Dashboard** - Statistiques et actions rapides
3. **Liste utilisateurs** - Tableau avec recherche/tri/filtres
4. **Créer utilisateur** - Formulaire avec validations
5. **Modifier utilisateur** - Formulaire pré-rempli
6. **Détails utilisateur** - Affichage complet
7. **Liste rôles** - Cartes avec informations
8. **Créer rôle** - Formulaire avec permissions
9. **Modifier rôle** - Formulaire pré-rempli
10. **Détails rôle** - Affichage avec utilisateurs associés

## 🔒 Validations côté serveur

### Utilisateur
- Nom : NotBlank, Length(min=2, max=100)
- Prénom : NotBlank, Length(min=2, max=100)
- Email : NotBlank, Email, UniqueEntity
- Password : NotBlank, Length(min=6)
- Role : NotNull

### Role
- Nom : NotBlank, Length(min=3, max=50), UniqueEntity
- Permissions : Array (optionnel)

## 📊 Permissions disponibles

### Gestion des utilisateurs
- `user.create` - Créer des utilisateurs
- `user.edit` - Modifier des utilisateurs
- `user.delete` - Supprimer des utilisateurs
- `user.view` - Voir les utilisateurs

### Gestion des rôles
- `role.create` - Créer des rôles
- `role.edit` - Modifier des rôles
- `role.delete` - Supprimer des rôles
- `role.view` - Voir les rôles

### Gestion des audits
- `audit.create` - Créer des audits
- `audit.edit` - Modifier des audits
- `audit.delete` - Supprimer des audits
- `audit.view` - Voir les audits
- `audit.report` - Générer des rapports

### Administration
- `admin.access` - Accès administration
- `admin.config` - Configuration système

## 🔧 Technologies utilisées

- **Framework** : Symfony 6.4
- **Base de données** : MySQL (XAMPP)
- **ORM** : Doctrine
- **Template** : Twig
- **CSS** : Bootstrap 5
- **Icônes** : Font Awesome 6
- **Validation** : Symfony Validator
- **Formulaires** : Symfony Forms

## 📝 Prochaines étapes suggérées

1. **Authentification** - Système de login/logout
2. **Autorisation** - Vérification des permissions
3. **Module Audits** - Gestion des audits
4. **IA Integration** - Questions automatiques
5. **Génération de rapports** - PDF/Excel
6. **Pagination** - Pour les grandes listes
7. **API REST** - Pour intégrations externes
8. **Tests unitaires** - PHPUnit
9. **Logs** - Historique des actions
10. **Notifications** - Email/SMS

## 📄 Licence

Ce projet est développé dans le cadre d'un projet académique.

## 👨‍💻 Auteur

Projet MindAudit - Gestion des Utilisateurs
#   m i n d  
 