# 📋 Résumé du Projet MindAudit - Gestion des Utilisateurs

## 🎯 Objectif du projet

Développer un module de **Gestion des Utilisateurs** pour la plateforme MindAudit, une application d'audit interne intelligente qui automatise les audits d'entreprise.

---

## ✅ Fonctionnalités implémentées

### 1. Gestion des Utilisateurs (CRUD complet)

#### ✅ Créer un utilisateur
- Formulaire avec validation côté serveur
- Champs : nom, prénom, email, mot de passe, rôle, statut actif
- Hash automatique du mot de passe (bcrypt)
- Validation email unique

#### ✅ Lire/Afficher les utilisateurs
- Liste complète avec tableau responsive
- Recherche par nom, prénom ou email
- Tri par nom, prénom, email ou date de création
- Filtrage par rôle
- Affichage des détails complets d'un utilisateur

#### ✅ Modifier un utilisateur
- Formulaire pré-rempli
- Possibilité de modifier le mot de passe (optionnel)
- Mise à jour des informations

#### ✅ Supprimer un utilisateur
- Confirmation via modal Bootstrap
- Protection CSRF
- Message de confirmation

### 2. Gestion des Rôles (CRUD complet)

#### ✅ Créer un rôle
- Formulaire avec nom, description et permissions
- Permissions sous forme de checkboxes groupées
- Validation côté serveur

#### ✅ Lire/Afficher les rôles
- Affichage en cartes (cards)
- Recherche par nom ou description
- Tri par nom ou date de création
- Compteur d'utilisateurs par rôle

#### ✅ Modifier un rôle
- Formulaire pré-rempli avec permissions
- Affichage du nombre d'utilisateurs affectés

#### ✅ Supprimer un rôle
- Vérification : impossible si des utilisateurs sont assignés
- Confirmation via modal
- Message d'erreur si suppression impossible

### 3. Dashboard

#### ✅ Statistiques
- Total des utilisateurs
- Nombre d'utilisateurs actifs
- Total des rôles
- Pourcentage d'utilisateurs actifs

#### ✅ Répartition
- Tableau des utilisateurs par rôle
- Graphique visuel de la répartition

#### ✅ Actions rapides
- Boutons d'accès rapide aux fonctionnalités principales

---

## 🏗️ Architecture technique

### Framework et technologies
- **Framework** : Symfony 6.4
- **Architecture** : MVC (Model-View-Controller)
- **Base de données** : MySQL (XAMPP)
- **ORM** : Doctrine
- **Template Engine** : Twig
- **Frontend** : Bootstrap 5 + Font Awesome 6

### Structure du projet
```
MindAudit/
├── src/
│   ├── Controller/
│   │   ├── HomeController.php          # Page d'accueil et dashboard
│   │   ├── UtilisateurController.php   # CRUD utilisateurs
│   │   └── RoleController.php          # CRUD rôles
│   ├── Entity/
│   │   ├── Utilisateur.php             # Entité utilisateur
│   │   └── Role.php                    # Entité rôle
│   ├── Form/
│   │   ├── UtilisateurType.php         # Formulaire utilisateur
│   │   └── RoleType.php                # Formulaire rôle
│   ├── Repository/
│   │   ├── UtilisateurRepository.php   # Requêtes utilisateurs
│   │   └── RoleRepository.php          # Requêtes rôles
│   └── DataFixtures/
│       └── AppFixtures.php             # Données de test
├── templates/
│   ├── base.html.twig                  # Template de base
│   ├── home/                           # Pages accueil/dashboard
│   ├── utilisateur/                    # Templates utilisateurs
│   └── role/                           # Templates rôles
└── config/                             # Configuration Symfony
```

---

## 🗄️ Base de données

### Entité Utilisateur
```sql
CREATE TABLE utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(180) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    actif BOOLEAN NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    role_id INT NOT NULL,
    FOREIGN KEY (role_id) REFERENCES role(id)
);
```

### Entité Role
```sql
CREATE TABLE role (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    permissions JSON NOT NULL,
    created_at DATETIME NOT NULL
);
```

### Relation
- **ManyToOne** : Utilisateur → Role
- **OneToMany** : Role → Utilisateurs

---

## 🎭 Les 3 Rôles du système

### 1. Administrateur
- Gestion complète du système
- Toutes les permissions
- Compte : admin@mindaudit.com / admin123

### 2. Auditeur
- Création et gestion des audits
- Génération de rapports
- Comptes : jean.dupont@mindaudit.com, sophie.martin@mindaudit.com / password123

### 3. Utilisateur
- Consultation des audits
- Participation aux processus
- Comptes : pierre.bernard@mindaudit.com, marie.leroy@mindaudit.com / password123

---

## ✅ Validations côté serveur

### Utilisateur
- ✅ Nom : NotBlank, Length(min=2, max=100)
- ✅ Prénom : NotBlank, Length(min=2, max=100)
- ✅ Email : NotBlank, Email, UniqueEntity
- ✅ Password : NotBlank, Length(min=6)
- ✅ Role : NotNull

### Role
- ✅ Nom : NotBlank, Length(min=3, max=50), UniqueEntity
- ✅ Description : Optionnel
- ✅ Permissions : Array (optionnel)

**Note importante** : Aucune validation HTML ou JavaScript n'a été utilisée, uniquement des validations côté serveur avec Symfony Validator.

---

## 🎨 Interface utilisateur

### Design
- Template Bootstrap 5 responsive
- Sidebar de navigation fixe
- Navbar avec liens fonctionnels
- Messages flash pour les notifications
- Icônes Font Awesome
- Cartes (cards) pour l'affichage
- Modals pour les confirmations
- Badges pour les statuts

### Pages implémentées
1. ✅ Page d'accueil avec présentation des 3 acteurs
2. ✅ Dashboard avec statistiques
3. ✅ Liste des utilisateurs (recherche/tri/filtres)
4. ✅ Créer un utilisateur
5. ✅ Modifier un utilisateur
6. ✅ Détails d'un utilisateur
7. ✅ Liste des rôles (cartes)
8. ✅ Créer un rôle
9. ✅ Modifier un rôle
10. ✅ Détails d'un rôle

---

## 🔧 Fonctionnalités avancées

### Recherche
- ✅ Recherche utilisateurs par nom, prénom ou email
- ✅ Recherche rôles par nom ou description
- ✅ Recherche en temps réel côté serveur

### Tri
- ✅ Tri des utilisateurs par nom, prénom, email, date
- ✅ Tri des rôles par nom ou date
- ✅ Ordre ascendant/descendant

### Filtrage
- ✅ Filtrage des utilisateurs par rôle
- ✅ Filtrage dynamique avec mise à jour de la liste

### Sécurité
- ✅ Hash des mots de passe (bcrypt)
- ✅ Protection CSRF sur les formulaires
- ✅ Validation des données côté serveur
- ✅ Email unique par utilisateur
- ✅ Nom de rôle unique

### Gestion des relations
- ✅ Affichage des utilisateurs par rôle
- ✅ Compteur d'utilisateurs par rôle
- ✅ Protection contre la suppression de rôles assignés
- ✅ Cascade des relations

---

## 📊 Données de test

### 3 Rôles créés
1. Administrateur (toutes permissions)
2. Auditeur (gestion audits)
3. Utilisateur (consultation)

### 6 Utilisateurs créés
- 1 Administrateur
- 2 Auditeurs
- 3 Utilisateurs (dont 1 inactif)

---

## 🚀 Installation et démarrage

### Commandes exécutées
```bash
# Création du projet
composer create-project symfony/skeleton:"6.4.*" MindAudit
composer require webapp

# Configuration base de données
DATABASE_URL="mysql://root:@127.0.0.1:3306/mindaudit"

# Création base de données et migrations
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate

# Chargement des données de test
composer require --dev orm-fixtures
php bin/console doctrine:fixtures:load

# Démarrage du serveur
symfony server:start
```

### URL d'accès
**http://127.0.0.1:8000/**

---

## 📝 Livrables

### Code source
- ✅ Entités avec annotations Doctrine
- ✅ Contrôleurs avec routes
- ✅ Formulaires avec validations
- ✅ Repositories avec méthodes personnalisées
- ✅ Templates Twig responsive
- ✅ Fixtures pour données de test

### Documentation
- ✅ README.md complet
- ✅ INSTRUCTIONS.md pour l'installation
- ✅ ROLES_ET_PERMISSIONS.md détaillé
- ✅ RESUME_PROJET.md (ce fichier)
- ✅ Commentaires dans le code

---

## 🎓 Respect du cahier des charges

### ✅ Exigences fonctionnelles
- [x] CRUD complet pour Utilisateur
- [x] CRUD complet pour Role
- [x] Relation ManyToOne entre Utilisateur et Role
- [x] Validations côté serveur uniquement
- [x] Recherche et tri implémentés
- [x] Templates Front/Back Office intégrés
- [x] Liens fonctionnels entre les pages

### ✅ Exigences techniques
- [x] Architecture MVC Symfony
- [x] Base de données MySQL (XAMPP)
- [x] Entités Doctrine
- [x] Formulaires Symfony
- [x] Templates Twig
- [x] Bootstrap pour le design
- [x] Pas de validation HTML/JS

### ✅ Acteurs et rôles
- [x] 3 rôles définis : Administrateur, Auditeur, Utilisateur
- [x] Permissions différenciées
- [x] Système de gestion des permissions

---

## 🏆 Points forts du projet

1. **Code propre et structuré** selon les standards Symfony
2. **Interface moderne** avec Bootstrap 5
3. **Validations robustes** côté serveur
4. **Recherche et filtres** performants
5. **Relations bien gérées** entre entités
6. **Documentation complète** et détaillée
7. **Données de test** prêtes à l'emploi
8. **Messages utilisateur** clairs et informatifs
9. **Design responsive** pour tous les écrans
10. **Sécurité** prise en compte (CSRF, hash passwords)

---

## 📈 Évolutions possibles

1. Système d'authentification (login/logout)
2. Vérification des permissions par rôle
3. Module de gestion des audits
4. Intégration de l'IA pour les questions
5. Génération de rapports PDF
6. Pagination pour les grandes listes
7. Export Excel des données
8. API REST pour intégrations
9. Tests unitaires et fonctionnels
10. Logs des actions utilisateurs

---

## 👨‍💻 Développeur

Projet réalisé dans le cadre du module de Gestion des Utilisateurs pour MindAudit.

**Date** : Février 2026
**Framework** : Symfony 6.4
**Base de données** : MySQL 8.0

---

## 📞 Support

Pour toute question :
- Documentation Symfony : https://symfony.com/doc
- Documentation Doctrine : https://www.doctrine-project.org/
- Documentation Bootstrap : https://getbootstrap.com/

---

**Projet MindAudit - Module Gestion des Utilisateurs ✅ TERMINÉ**
