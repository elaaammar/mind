# Configuration OAuth - Google et Facebook

Ce document explique comment configurer l'authentification OAuth avec Google et Facebook pour votre application MindAudit.

## 📋 Prérequis

- Compte Google Developer
- Compte Facebook Developer
- Application déployée avec une URL accessible (localhost pour le développement)

---

## 🔵 Configuration Google OAuth

### 1. Créer un projet Google Cloud

1. Allez sur [Google Cloud Console](https://console.cloud.google.com/)
2. Créez un nouveau projet ou sélectionnez un projet existant
3. Activez l'API "Google+ API" pour votre projet

### 2. Créer des identifiants OAuth 2.0

1. Dans le menu, allez à **APIs & Services** > **Credentials**
2. Cliquez sur **Create Credentials** > **OAuth client ID**
3. Configurez l'écran de consentement OAuth si ce n'est pas déjà fait
4. Sélectionnez **Web application** comme type d'application
5. Configurez les paramètres :
   - **Name**: MindAudit (ou le nom de votre choix)
   - **Authorized JavaScript origins**: 
     - `http://localhost:8000` (pour le développement)
     - `https://votre-domaine.com` (pour la production)
   - **Authorized redirect URIs**:
     - `http://localhost:8000/connect/google/check` (développement)
     - `https://votre-domaine.com/connect/google/check` (production)

### 3. Récupérer les identifiants

Après la création, vous obtiendrez :
- **Client ID** : ressemble à `123456789-abcdefg.apps.googleusercontent.com`
- **Client Secret** : une chaîne de caractères aléatoire

### 4. Configurer dans votre application

Ajoutez ces valeurs dans votre fichier `.env` :

```env
GOOGLE_CLIENT_ID=votre_client_id_google
GOOGLE_CLIENT_SECRET=votre_client_secret_google
```

---

## 🔷 Configuration Facebook OAuth

### 1. Créer une application Facebook

1. Allez sur [Facebook Developers](https://developers.facebook.com/)
2. Cliquez sur **My Apps** > **Create App**
3. Sélectionnez **Consumer** comme type d'application
4. Remplissez les informations de base :
   - **App Name**: MindAudit
   - **App Contact Email**: votre email

### 2. Configurer Facebook Login

1. Dans le tableau de bord de votre application, ajoutez le produit **Facebook Login**
2. Sélectionnez **Web** comme plateforme
3. Configurez les paramètres :
   - **Site URL**: `http://localhost:8000` (développement) ou `https://votre-domaine.com` (production)

### 3. Configurer les URI de redirection OAuth

1. Allez dans **Facebook Login** > **Settings**
2. Dans **Valid OAuth Redirect URIs**, ajoutez :
   - `http://localhost:8000/connect/facebook/check` (développement)
   - `https://votre-domaine.com/connect/facebook/check` (production)
3. Sauvegardez les modifications

### 4. Récupérer les identifiants

1. Allez dans **Settings** > **Basic**
2. Vous trouverez :
   - **App ID** : votre identifiant d'application
   - **App Secret** : cliquez sur "Show" pour voir le secret (vous devrez peut-être entrer votre mot de passe Facebook)

### 5. Configurer dans votre application

Ajoutez ces valeurs dans votre fichier `.env` :

```env
FACEBOOK_CLIENT_ID=votre_app_id_facebook
FACEBOOK_CLIENT_SECRET=votre_app_secret_facebook
```

### 6. Passer en mode Production (Important!)

Par défaut, votre application Facebook est en mode "Development". Pour que d'autres utilisateurs puissent se connecter :

1. Allez dans **Settings** > **Basic**
2. Remplissez tous les champs requis (Privacy Policy URL, Terms of Service URL, etc.)
3. En haut de la page, basculez le mode de **Development** à **Live**

---

## 🧪 Test de la configuration

### En développement local

1. Assurez-vous que votre serveur Symfony est lancé : `symfony server:start` ou `php -S localhost:8000 -t public`
2. Allez sur `http://localhost:8000/login`
3. Cliquez sur le bouton **Google** ou **Facebook**
4. Autorisez l'application à accéder à vos informations
5. Vous devriez être redirigé vers le dashboard après authentification

### Vérification des routes

Vérifiez que les routes OAuth sont bien configurées :

```bash
php bin/console debug:router | findstr "connect"
```

Vous devriez voir :
- `connect_google` : `/connect/google`
- `connect_google_check` : `/connect/google/check`
- `connect_facebook` : `/connect/facebook`
- `connect_facebook_check` : `/connect/facebook/check`

---

## 🔒 Sécurité

### Variables d'environnement

**IMPORTANT** : Ne commitez JAMAIS vos identifiants OAuth dans Git !

- Les identifiants doivent être dans `.env.local` (ignoré par Git)
- Le fichier `.env` contient uniquement des valeurs par défaut/exemples
- Pour la production, utilisez des variables d'environnement serveur

### HTTPS en production

En production, utilisez **TOUJOURS** HTTPS :
- Les providers OAuth (Google, Facebook) exigent HTTPS pour la sécurité
- Configurez un certificat SSL (Let's Encrypt est gratuit)
- Mettez à jour les redirect URIs avec `https://`

---

## 🐛 Dépannage

### Erreur "redirect_uri_mismatch"

- Vérifiez que l'URI de redirection dans la console Google/Facebook correspond **exactement** à celle de votre application
- Attention aux trailing slashes (`/` à la fin)
- Vérifiez le protocole (http vs https)

### Erreur "invalid_client"

- Vérifiez que le Client ID et Client Secret sont corrects dans `.env`
- Assurez-vous qu'il n'y a pas d'espaces avant/après les valeurs

### L'utilisateur n'est pas créé

- Vérifiez que le rôle "Utilisateur" existe dans votre base de données
- Consultez les logs Symfony : `var/log/dev.log`

### Erreur "App Not Setup"

- Pour Facebook : votre application est peut-être encore en mode Development
- Passez-la en mode Live ou ajoutez votre compte de test dans les testeurs de l'application

---

## 📚 Ressources

- [Documentation Google OAuth 2.0](https://developers.google.com/identity/protocols/oauth2)
- [Documentation Facebook Login](https://developers.facebook.com/docs/facebook-login)
- [KnpUOAuth2ClientBundle](https://github.com/knpuniversity/oauth2-client-bundle)

---

## ✅ Checklist de configuration

- [ ] Projet Google Cloud créé
- [ ] Identifiants OAuth Google configurés
- [ ] Application Facebook créée
- [ ] Facebook Login configuré
- [ ] Variables d'environnement ajoutées dans `.env.local`
- [ ] Routes OAuth testées
- [ ] Rôle "Utilisateur" existe dans la base de données
- [ ] Connexion Google testée
- [ ] Connexion Facebook testée
- [ ] Application Facebook en mode Live (pour la production)
