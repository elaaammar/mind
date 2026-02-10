# Instructions d'installation et de démarrage - MindAudit

## Prérequis
- XAMPP installé avec MySQL
- PHP 8.1 ou supérieur
- Composer installé
- Symfony CLI installé

## Étapes d'installation

### 1. Démarrer XAMPP
- Ouvrez XAMPP Control Panel
- Démarrez Apache et MySQL

### 2. Créer la base de données
```bash
# Dans le dossier MindAudit
php bin/console doctrine:database:create
```

### 3. Créer les migrations
```bash
php bin/console make:migration
```

### 4. Exécuter les migrations
```bash
php bin/console doctrine:migrations:migrate
```

### 5. (Optionnel) Charger des données de test
Créez un fichier de fixtures pour ajouter des données de test :

```bash
composer require --dev orm-fixtures
php bin/console make:fixtures
```

### 6. Démarrer le serveur Symfony
```bash
symfony server:start
```
OU
```bash
php -S localhost:8000 -t public
```

### 7. Accéder à l'application
Ouvrez votre navigateur et accédez à :
- Page d'accueil : http://localhost:8000/
- Dashboard : http://localhost:8000/dashboard
- Utilisateurs : http://localhost:8000/utilisateur
- Rôles : http://localhost:8000/role

## Structure du projet

### Entités créées
1. **Utilisateur** (id, nom, prenom, email, password, actif, createdAt, role)
2. **Role** (id, nom, description, permissions, createdAt)

### Relations
- Un Utilisateur appartient à un Role (ManyToOne)
- Un Role peut avoir plusieurs Utilisateurs (OneToMany)

### Fonctionnalités implémentées

#### Gestion des Utilisateurs
- ✅ Liste avec recherche et tri
- ✅ Création avec validation côté serveur
- ✅ Modification
- ✅ Suppression avec confirmation
- ✅ Affichage des détails
- ✅ Filtrage par rôle
- ✅ Hash automatique des mots de passe

#### Gestion des Rôles
- ✅ Liste avec recherche
- ✅ Création avec permissions
- ✅ Modification
- ✅ Suppression (avec vérification des utilisateurs associés)
- ✅ Affichage des détails
- ✅ Gestion des permissions (checkboxes)

#### Dashboard
- ✅ Statistiques (total utilisateurs, actifs, rôles)
- ✅ Répartition des utilisateurs par rôle
- ✅ Actions rapides

### Validations côté serveur
- Nom et prénom : minimum 2 caractères
- Email : format valide et unique
- Mot de passe : minimum 6 caractères
- Rôle : obligatoire pour un utilisateur
- Nom du rôle : unique

### Templates
- Template de base avec Bootstrap 5
- Sidebar de navigation
- Messages flash pour les notifications
- Design responsive
- Icônes Font Awesome

## Prochaines étapes suggérées

### 1. Créer des fixtures pour les données de test
```php
// src/DataFixtures/AppFixtures.php
```

### 2. Ajouter l'authentification
```bash
composer require symfony/security-bundle
php bin/console make:user
php bin/console make:auth
```

### 3. Ajouter la pagination
```bash
composer require knplabs/knp-paginator-bundle
```

### 4. Ajouter l'export PDF/Excel
```bash
composer require dompdf/dompdf
composer require phpoffice/phpspreadsheet
```

### 5. Ajouter des tests
```bash
php bin/console make:test
```

## Dépannage

### Erreur de connexion à la base de données
- Vérifiez que MySQL est démarré dans XAMPP
- Vérifiez les paramètres dans le fichier `.env`
- Port par défaut : 3306

### Erreur de permissions
- Assurez-vous que le dossier `var/` est accessible en écriture

### Erreur de cache
```bash
php bin/console cache:clear
```

## Contact et support
Pour toute question, consultez la documentation Symfony : https://symfony.com/doc
