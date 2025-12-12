# Refactorisation du Système d'Authentification

## Vue d'ensemble

Ce document détaille la refactorisation du système d'authentification avec des restrictions basées sur les indicatifs téléphoniques et des rôles séparés pour les utilisateurs publics et les administrateurs.

## 📋 Règles d'Authentification

### 1. Interface Utilisateur Public (Grand Public - Sénégal)

**URL:** `/login`

**Restrictions:**
- ✅ **Indicatif verrouillé:** +221 (Sénégal)
- ✅ **Validation stricte:** Seuls les numéros sénégalais sont acceptés
- ✅ **Format:** 9 chiffres (ex: 77 XXX XX XX)

**Exceptions pour tests:**
Les deux numéros ivoiriens suivants sont autorisés en mode test (hardcodés) :
- `+2250545029721`
- `+2250748348221`

### 2. Interface Administrateur (Accès Séparé)

**URL:** `/admin/login`

**Restrictions:**
- ✅ **Indicatif verrouillé:** +225 (Côte d'Ivoire)
- ✅ **Validation ultra-stricte:** Seul le numéro `+2250748348221` est autorisé
- ✅ **Rôle requis:** `role = 'admin'` dans la base de données
- ✅ **Format:** 10 chiffres (ex: 07 48 34 82 21)

## 🔧 Fichiers Créés/Modifiés

### Nouveaux Fichiers

1. **`config/auth_phones.php`**
   - Configuration centralisée des numéros autorisés
   - Whitelist des numéros de test CI
   - Numéro administrateur

2. **`app/Http/Controllers/Web/AdminAuthController.php`**
   - Contrôleur dédié à l'authentification admin
   - Validation stricte du numéro admin
   - Logique OTP séparée

3. **`app/Http/Middleware/CheckAdmin.php`**
   - Middleware de protection des routes admin
   - Vérification du rôle admin

4. **`resources/views/admin/auth/login.blade.php`**
   - Interface de connexion admin avec design distinct
   - Indicatif +225 verrouillé visuellement

5. **`database/seeders/AdminUserSeeder.php`**
   - Seeder pour créer/mettre à jour l'utilisateur admin
   - Vérification des numéros de test

### Fichiers Modifiés

1. **`app/Http/Controllers/Web/AuthController.php`**
   - Ajout de la méthode `isPhoneAllowedForPublic()`
   - Validation stricte avec exceptions CI en whitelist
   - Double vérification dans `sendOtp()` et `verifyOtp()`

2. **`resources/views/auth/login.blade.php`**
   - Indicatif +221 verrouillé (non modifiable)
   - Suppression du sélecteur de pays
   - Simplification du JavaScript

3. **`routes/web.php`**
   - Ajout des routes admin auth (`/admin/login`, `/admin/auth/*`)
   - Application du middleware `check.admin` sur les routes admin existantes

4. **`bootstrap/app.php`**
   - Enregistrement du middleware `check.admin`

5. **`database/seeders/DatabaseSeeder.php`**
   - Appel du `AdminUserSeeder`

## 🚀 Installation et Configuration

### 1. Exécuter les Seeders

```bash
php artisan db:seed --class=AdminUserSeeder
```

Ou pour réinitialiser toute la base :

```bash
php artisan migrate:fresh --seed
```

### 2. Vérification de la Configuration

Le fichier `config/auth_phones.php` contient :

```php
'test_phones_ci' => [
    '+2250545029721',
    '+2250748348221',
],

'admin_phone' => '+2250748348221',
```

## 🔐 Flux d'Authentification

### Utilisateur Public (Sénégal)

1. **Accès:** `/login`
2. **Saisie:** Nom + Numéro (format: 77 XXX XX XX)
3. **Validation:**
   - Doit commencer par +221 OU
   - Être dans la whitelist CI (`test_phones_ci`)
4. **OTP:** Envoyé via WhatsApp
5. **Vérification:** Code à 6 chiffres
6. **Redirection:** `/matches`

### Administrateur (Côte d'Ivoire)

1. **Accès:** `/admin/login`
2. **Saisie:** Nom + Numéro (doit être exactement `0748348221`)
3. **Validation:**
   - Doit être exactement `+2250748348221`
   - Rejette tous les autres numéros
4. **OTP:** Envoyé via WhatsApp avec message admin
5. **Vérification:** Code à 6 chiffres
6. **Attribution auto:** Rôle `admin` assigné automatiquement
7. **Redirection:** `/admin`

## 🛡️ Sécurité

### Protection des Routes Admin

Toutes les routes sous `/admin` (sauf `/admin/login` et `/admin/auth/*`) sont protégées par le middleware `check.admin` qui :

1. Vérifie la présence d'une session utilisateur
2. Vérifie que l'utilisateur a le rôle `admin`
3. Redirige vers `/admin/login` si non autorisé

### Logs de Sécurité

Le système log automatiquement :
- Les tentatives de connexion admin avec des numéros non autorisés
- Les tentatives d'inscription publique avec des numéros non sénégalais
- Les numéros CI autorisés en mode test

## 📊 Base de Données

### Colonne `role` dans la table `users`

La colonne `role` doit exister avec les valeurs possibles :
- `user` (défaut) - Utilisateur standard
- `admin` - Administrateur

Si la migration n'existe pas encore, créez-la :

```bash
php artisan make:migration add_role_to_users_table --table=users
```

Puis ajoutez :

```php
$table->string('role')->default('user');
```

## 🧪 Tests

### Test Utilisateur Public (Sénégal)

```
Numéro: 77 123 45 67
Indicatif: +221 (verrouillé)
Résultat: ✅ Autorisé
```

### Test Utilisateur Public (Exception CI)

```
Numéro: 05 45 02 97 21
Indicatif: +225 (via whitelist)
Résultat: ✅ Autorisé (mode test)
```

### Test Utilisateur Public (Non Autorisé)

```
Numéro: 07 12 34 56 78 (CI non whitelisté)
Indicatif: +225
Résultat: ❌ Refusé
Message: "Ce numéro n'est pas autorisé. Seuls les numéros sénégalais (+221) sont acceptés."
```

### Test Admin

```
Numéro: 07 48 34 82 21
Indicatif: +225 (verrouillé)
Résultat: ✅ Autorisé avec rôle admin
```

### Test Admin (Mauvais Numéro)

```
Numéro: 07 12 34 56 78
Indicatif: +225
Résultat: ❌ Refusé
Message: "Accès non autorisé. Ce numéro n'a pas les droits d'administrateur."
```

## 📞 Support

Pour ajouter/retirer des numéros de test CI, modifiez `config/auth_phones.php` :

```php
'test_phones_ci' => [
    '+2250545029721',
    '+2250748348221',
    '+2250XXXXXXXXX', // Ajouter ici
],
```

Puis redémarrez l'application ou videz le cache :

```bash
php artisan config:clear
```

## 🎨 Interface Utilisateur

### Page Login Public
- Thème: Bleu/Orange SOBOA
- Indicatif: 🇸🇳 +221 (verrouillé, gris)
- Message WhatsApp: Standard

### Page Login Admin
- Thème: Rouge/Noir (sécurisé)
- Indicatif: 🇨🇮 +225 (verrouillé, gris)
- Message WhatsApp: "Code d'accès administrateur"
- Icônes: Cadenas, Bouclier
- Avertissement: "Zone d'administration sécurisée"
