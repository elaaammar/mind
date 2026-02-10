# 🚀 Guide Rapide - Authentification OAuth

## Configuration en 5 minutes

### 1️⃣ Créer les applications OAuth

#### Google
1. Allez sur https://console.cloud.google.com/
2. Créez un projet
3. APIs & Services > Credentials > Create OAuth Client ID
4. Type: Web Application
5. Redirect URI: `http://localhost:8000/connect/google/check`

#### Facebook
1. Allez sur https://developers.facebook.com/
2. My Apps > Create App > Consumer
3. Ajoutez Facebook Login
4. Valid OAuth Redirect URIs: `http://localhost:8000/connect/facebook/check`

### 2️⃣ Configurer les identifiants

Créez un fichier `.env.local` à la racine du projet :

```env
GOOGLE_CLIENT_ID=votre_client_id_google
GOOGLE_CLIENT_SECRET=votre_client_secret_google
FACEBOOK_CLIENT_ID=votre_app_id_facebook
FACEBOOK_CLIENT_SECRET=votre_app_secret_facebook
```

### 3️⃣ Vérifier le rôle par défaut

Assurez-vous qu'un rôle "Utilisateur" existe dans votre base de données :

```sql
SELECT * FROM role WHERE nom = 'Utilisateur';
```

Si ce n'est pas le cas, créez-le via l'interface admin ou avec SQL :

```sql
INSERT INTO role (nom, permissions) VALUES ('Utilisateur', '[]');
```

### 4️⃣ Tester

1. Démarrez votre serveur : `symfony server:start` ou `php -S localhost:8000 -t public`
2. Allez sur http://localhost:8000/login
3. Cliquez sur "Google" ou "Facebook"
4. Autorisez l'application
5. Vous êtes connecté ! 🎉

## 📝 Notes importantes

- **Développement** : Utilisez `http://localhost:8000`
- **Production** : Utilisez `https://votre-domaine.com` et mettez à jour les redirect URIs
- **Facebook** : Passez l'app en mode "Live" pour que d'autres utilisateurs puissent se connecter
- **Sécurité** : Ne commitez JAMAIS le fichier `.env.local` dans Git

## 🔍 Vérification

Routes OAuth disponibles :
```bash
php bin/console debug:router | findstr "connect"
```

Vous devriez voir :
- `/connect/google` - Initie la connexion Google
- `/connect/google/check` - Callback Google
- `/connect/facebook` - Initie la connexion Facebook
- `/connect/facebook/check` - Callback Facebook

## 🆘 Problèmes courants

**"redirect_uri_mismatch"**
→ Vérifiez que l'URI dans la console Google/Facebook correspond exactement

**"invalid_client"**
→ Vérifiez vos identifiants dans `.env.local`

**"App Not Setup" (Facebook)**
→ Passez votre app en mode "Live" ou ajoutez-vous comme testeur

Pour plus de détails, consultez `OAUTH_CONFIGURATION.md`
