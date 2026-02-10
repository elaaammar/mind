# 🎭 Rôles et Permissions - MindAudit

## Vue d'ensemble

Le système MindAudit est conçu autour de **3 acteurs principaux** avec des rôles et permissions distincts.

---

## 1. 👨‍💼 Administrateur

### Description
L'administrateur a un accès complet au système. Il est responsable de la gestion des utilisateurs, des rôles et de la configuration globale de la plateforme.

### Responsabilités
- Gérer tous les utilisateurs du système
- Créer, modifier et supprimer des rôles
- Configurer les paramètres du système
- Superviser toutes les activités d'audit
- Gérer les permissions et les accès

### Permissions
```
✅ user.create      - Créer des utilisateurs
✅ user.edit        - Modifier des utilisateurs
✅ user.delete      - Supprimer des utilisateurs
✅ user.view        - Voir les utilisateurs

✅ role.create      - Créer des rôles
✅ role.edit        - Modifier des rôles
✅ role.delete      - Supprimer des rôles
✅ role.view        - Voir les rôles

✅ audit.create     - Créer des audits
✅ audit.edit       - Modifier des audits
✅ audit.delete     - Supprimer des audits
✅ audit.view       - Voir les audits
✅ audit.report     - Générer des rapports

✅ admin.access     - Accès administration
✅ admin.config     - Configuration système
```

### Cas d'usage
- Créer un nouveau compte auditeur
- Modifier les permissions d'un rôle
- Désactiver un compte utilisateur
- Configurer les paramètres de l'application
- Superviser tous les audits en cours

---

## 2. 🔍 Auditeur

### Description
L'auditeur est le responsable des audits internes. Il crée, gère et analyse les audits, et génère les rapports nécessaires pour l'organisation.

### Responsabilités
- Créer et planifier des audits
- Gérer le processus d'audit
- Collecter et analyser les données
- Générer des rapports d'audit
- Suivre l'historique des audits

### Permissions
```
✅ user.view        - Voir les utilisateurs

✅ role.view        - Voir les rôles

✅ audit.create     - Créer des audits
✅ audit.edit       - Modifier des audits
✅ audit.delete     - Supprimer des audits
✅ audit.view       - Voir les audits
✅ audit.report     - Générer des rapports
```

### Cas d'usage
- Lancer un nouvel audit interne
- Poser des questions via l'IA aux responsables
- Collecter les documents nécessaires
- Analyser les réponses et documents
- Générer un rapport d'audit détaillé
- Consulter l'historique des audits précédents

---

## 3. 👤 Utilisateur

### Description
L'utilisateur standard représente les membres de l'organisation qui participent aux audits. Ils peuvent consulter les informations et participer aux processus d'audit.

### Responsabilités
- Répondre aux questions d'audit
- Fournir les documents demandés
- Consulter les audits qui les concernent
- Participer aux processus d'amélioration

### Permissions
```
✅ user.view        - Voir les utilisateurs

✅ audit.view       - Voir les audits
```

### Cas d'usage
- Consulter un audit en cours
- Répondre aux questions de l'auditeur
- Fournir des documents pour l'audit
- Voir les résultats d'audit
- Consulter l'historique des audits

---

## 📊 Tableau comparatif des permissions

| Permission | Administrateur | Auditeur | Utilisateur |
|------------|:--------------:|:--------:|:-----------:|
| **Gestion Utilisateurs** |
| Créer utilisateur | ✅ | ❌ | ❌ |
| Modifier utilisateur | ✅ | ❌ | ❌ |
| Supprimer utilisateur | ✅ | ❌ | ❌ |
| Voir utilisateurs | ✅ | ✅ | ✅ |
| **Gestion Rôles** |
| Créer rôle | ✅ | ❌ | ❌ |
| Modifier rôle | ✅ | ❌ | ❌ |
| Supprimer rôle | ✅ | ❌ | ❌ |
| Voir rôles | ✅ | ✅ | ❌ |
| **Gestion Audits** |
| Créer audit | ✅ | ✅ | ❌ |
| Modifier audit | ✅ | ✅ | ❌ |
| Supprimer audit | ✅ | ✅ | ❌ |
| Voir audits | ✅ | ✅ | ✅ |
| Générer rapports | ✅ | ✅ | ❌ |
| **Administration** |
| Accès admin | ✅ | ❌ | ❌ |
| Configuration | ✅ | ❌ | ❌ |

---

## 🔐 Comptes de test

| Email | Mot de passe | Rôle | Statut |
|-------|--------------|------|--------|
| admin@mindaudit.com | admin123 | Administrateur | Actif |
| jean.dupont@mindaudit.com | password123 | Auditeur | Actif |
| sophie.martin@mindaudit.com | password123 | Auditeur | Actif |
| pierre.bernard@mindaudit.com | password123 | Utilisateur | Actif |
| marie.leroy@mindaudit.com | password123 | Utilisateur | Actif |
| luc.dubois@mindaudit.com | password123 | Utilisateur | Inactif |

---

## 🔄 Workflow typique

### Scénario : Audit interne d'un département

1. **Administrateur** crée les comptes nécessaires
   - Crée un compte auditeur pour Jean Dupont
   - Crée des comptes utilisateurs pour les employés du département

2. **Auditeur** (Jean Dupont) lance l'audit
   - Crée un nouvel audit "Audit Département RH 2026"
   - L'IA génère les questions pertinentes
   - Envoie les questions aux utilisateurs concernés

3. **Utilisateurs** participent à l'audit
   - Pierre Bernard répond aux questions
   - Marie Leroy fournit les documents demandés

4. **Auditeur** analyse et conclut
   - Analyse les réponses et documents
   - L'IA aide à identifier les points critiques
   - Génère le rapport d'audit final

5. **Administrateur** supervise
   - Consulte le rapport
   - Prend les décisions nécessaires
   - Archive l'audit dans l'historique

---

## 📝 Notes importantes

- Les permissions sont cumulatives : un administrateur a toutes les permissions
- Un utilisateur ne peut pas modifier son propre rôle
- La suppression d'un rôle est impossible s'il est assigné à des utilisateurs
- L'historique des audits est conservé pour améliorer les analyses futures
- L'IA s'améliore avec chaque audit réalisé

---

## 🚀 Évolutions futures

- Ajout de rôles personnalisés
- Permissions granulaires par département
- Délégation temporaire de permissions
- Audit trail des actions sensibles
- Notifications par email/SMS selon les rôles
