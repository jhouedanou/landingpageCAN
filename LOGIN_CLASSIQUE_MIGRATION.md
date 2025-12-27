# Migration vers Login Classique

## Vue d'ensemble

Cette branche implémente un système de login classique avec numéro de téléphone + mot de passe personnalisé, tout en conservant la compatibilité avec les utilisateurs existants qui utilisent des codes OTP.

## Changements majeurs

### 1. Nouvelle page de login (`/login`)
- Formulaire simplifié : numéro de téléphone + mot de passe
- Indicatif fixe : +221 (Sénégal uniquement)
- Champ mot de passe avec visibilité toggle
- Compatible avec anciens utilisateurs (code OTP à 6 chiffres)

### 2. Nouvelle page d'inscription (`/register`)
- Création de compte avec mot de passe personnalisé
- Indicateur de force du mot de passe
- Validation : minimum 6 caractères
- Confirmation du mot de passe requise

### 3. AuthController mis à jour
- **Nouvelle méthode `login()`** : Authentification avec numéro + mot de passe
- **Nouvelle méthode `register()`** : Inscription avec mot de passe personnalisé
- **Compatibilité rétroactive** : Vérifie d'abord `password`, puis `otp_password` pour les anciens utilisateurs

### 4. Session longue durée
- **Durée de session** : Jusqu'en février 2026 (~604 800 minutes)
- **Cookie remember_token** : Expire également en février 2026
- **Reconnexion automatique** : Via cookie persistant

### 5. Commande de migration
```bash
php artisan users:migrate-otp-to-password
```
Cette commande copie les `otp_password` existants vers le champ `password` pour permettre aux anciens utilisateurs de se connecter avec leur code OTP comme mot de passe.

## Compatibilité avec les utilisateurs existants

### Ancien système (OTP)
- Utilisateurs avec `otp_password` défini
- Peuvent se connecter avec leur code à 6 chiffres
- Le système vérifie `otp_password` si `password` est vide

### Nouveau système (Password)
- Nouveaux utilisateurs avec `password` défini
- Peuvent choisir un mot de passe personnalisé (6+ caractères)
- Le système vérifie d'abord `password`

### Après migration
```bash
php artisan users:migrate-otp-to-password
```
- Les `otp_password` sont copiés vers `password`
- Tous les utilisateurs peuvent se connecter via le nouveau formulaire
- Les anciens utilisent leur code OTP comme mot de passe

## Routes ajoutées

```php
// Login classique
GET  /login                 -> Page de connexion
POST /auth/login           -> Authentification
GET  /register             -> Page d'inscription
POST /auth/register        -> Création de compte

// Ancien système OTP (conservé pour compatibilité)
POST /auth/send-otp
POST /auth/verify-otp
POST /auth/request-new-code
```

## Structure des données

### Table `users`
- `password` : Mot de passe principal (hash bcrypt)
- `otp_password` : Ancien code OTP (hash bcrypt) - conservé pour compatibilité
- `remember_token` : Token de session persistante
- `last_login_at` : Dernière connexion

## Déploiement

### Étapes de déploiement

1. **Merger la branche**
```bash
git checkout main
git merge login-classique
```

2. **Migrer les utilisateurs existants**
```bash
php artisan users:migrate-otp-to-password
```

3. **Vider le cache**
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

4. **Tester la connexion**
- Ancien utilisateur : Utiliser le code OTP à 6 chiffres
- Nouvel utilisateur : S'inscrire avec un mot de passe personnalisé

## Tests à effectuer

### Test 1 : Ancien utilisateur
1. Se connecter avec numéro + code OTP (6 chiffres)
2. Vérifier que la connexion fonctionne
3. Vérifier la session persistante (fermer/rouvrir le navigateur)

### Test 2 : Nouvel utilisateur
1. S'inscrire avec numéro + mot de passe personnalisé
2. Se connecter avec les mêmes identifiants
3. Vérifier la session persistante

### Test 3 : Validation
1. Tenter de s'inscrire avec un numéro existant → Erreur
2. Tenter de se connecter avec mauvais mot de passe → Erreur
3. Tenter de s'inscrire avec mot de passe < 6 caractères → Erreur

## Sécurité

- **Rate limiting** : 10 tentatives/min pour login, 5/min pour register
- **Passwords hashés** : Bcrypt avec coût par défaut
- **Cookie sécurisé** : HttpOnly + Secure (HTTPS)
- **Session chiffrée** : Driver database avec encryption

## Notes importantes

1. **Anciens utilisateurs** : Leur code OTP à 6 chiffres devient leur mot de passe
2. **Nouveaux utilisateurs** : Peuvent choisir un mot de passe personnalisé
3. **Compatibilité** : Le système vérifie les deux champs (`password` et `otp_password`)
4. **Session longue** : Les utilisateurs restent connectés jusqu'en février 2026
5. **Indicatif unique** : Seul +221 (Sénégal) est accepté

## Support

Pour toute question ou problème :
- Vérifier les logs : `storage/logs/laravel.log`
- Tester la commande de migration : `php artisan users:migrate-otp-to-password`
- Vérifier la config session : `config/session.php`
