# Guide de Correction - Équipes et Drapeaux

## ✅ Corrections Effectuées

### 1. TOUS les drapeaux utilisent maintenant flagicons.lipis.dev
- 12 fichiers modifiés automatiquement
- Format: `https://flagicons.lipis.dev/flags/4x3/{iso_code}.svg`
- Meilleure qualité et rendu SVG

### 2. Légende ajoutée sous la carte /map
- 4 types de PDV avec leurs icônes et couleurs
- Responsive et bien stylisée

### 3. Fichiers créés pour corriger les équipes
- `database/seeders/TeamIsoCodesSeeder.php`
- `database/sql/add_team_iso_codes.sql`

---

## 🚀 ACTIONS À FAIRE (Dans Docker)

### Étape 1: Réimporter les équipes depuis le CSV

```bash
# Entrer dans le conteneur Docker
docker exec -it landingpagecan-laravel.test-1 bash

# Option A: Réinitialisation complète (recommandé)
php artisan migrate:fresh
php artisan db:seed --class=FreshDeploymentSeeder

# OU Option B: Juste ajouter les ISO codes aux équipes existantes
php artisan db:seed --class=TeamIsoCodesSeeder
```

### Étape 2: Vérifier les équipes

```bash
php artisan tinker
>>> App\Models\Team::count()
# Devrait afficher: 8

>>> App\Models\Team::orderBy('name')->get(['name', 'iso_code'])
# Devrait afficher toutes les équipes avec leurs iso_code
```

---

## 📋 Les 8 Équipes du CSV

| Équipe | ISO Code | Drapeau |
|--------|----------|---------|
| SENEGAL | sn | 🇸🇳 |
| BOTSWANA | bw | 🇧🇼 |
| AFRIQUE DU SUD | za | 🇿🇦 |
| EGYPTE | eg | 🇪🇬 |
| RD CONGO | cd | 🇨🇩 |
| COTE D'IVOIRE | ci | 🇨🇮 |
| CAMEROUN | cm | 🇨🇲 |
| BENIN | bj | 🇧🇯 |

---

## 🔍 Diagnostic des Problèmes

### Problème: Les drapeaux ne s'affichent pas

**Cause possible 1:** Les équipes n'ont pas de `iso_code`

**Solution:**
```bash
docker exec -it landingpagecan-laravel.test-1 php artisan db:seed --class=TeamIsoCodesSeeder
```

**Cause possible 2:** Les équipes n'existent pas dans la base

**Solution:**
```bash
docker exec -it landingpagecan-laravel.test-1 php artisan db:seed --class=FreshDeploymentSeeder
```

### Problème: Certaines équipes manquent

**Vérification:**
```bash
# Dans Docker
docker exec -it landingpagecan-laravel.test-1 php artisan tinker

# Compter les équipes
>>> App\Models\Team::count()

# Lister les équipes
>>> App\Models\Team::pluck('name')
```

**Si moins de 8 équipes:** Réexécuter le seeder complet
```bash
docker exec -it landingpagecan-laravel.test-1 php artisan migrate:fresh
docker exec -it landingpagecan-laravel.test-1 php artisan db:seed --class=FreshDeploymentSeeder
```

---

## 📝 Script SQL Direct (Si Seeders ne fonctionnent pas)

Le fichier `database/sql/add_team_iso_codes.sql` contient les requêtes SQL directes:

```bash
# Dans Docker, se connecter à MySQL/PostgreSQL
docker exec -it landingpagecan-mysql-1 mysql -u sail -p

# Ou pour SQLite
docker exec -it landingpagecan-laravel.test-1 php artisan db
```

Puis copier-coller les requêtes du fichier SQL:
```sql
UPDATE teams SET iso_code = 'sn' WHERE UPPER(name) = 'SENEGAL';
UPDATE teams SET iso_code = 'bw' WHERE UPPER(name) = 'BOTSWANA';
UPDATE teams SET iso_code = 'za' WHERE UPPER(name) = 'AFRIQUE DU SUD';
UPDATE teams SET iso_code = 'eg' WHERE UPPER(name) = 'EGYPTE';
UPDATE teams SET iso_code = 'cd' WHERE UPPER(name) = 'RD CONGO';
UPDATE teams SET iso_code = 'ci' WHERE UPPER(name) LIKE '%COTE%IVOIRE%';
UPDATE teams SET iso_code = 'cm' WHERE UPPER(name) = 'CAMEROUN';
UPDATE teams SET iso_code = 'bj' WHERE UPPER(name) = 'BENIN';
```

---

## 🧪 Tests après Correction

### 1. Tester les drapeaux sur /matches
- Visiter `/matches`
- Tous les matchs devraient afficher les drapeaux des équipes
- Format SVG, meilleure qualité

### 2. Tester la carte sur /map
- Visiter `/map`
- Voir les 4 types d'icônes différentes (bleu, vert, orange, violet)
- La légende s'affiche en bas de la carte
- Cliquer sur un marqueur pour voir les détails

### 3. Tester l'admin
- Visiter `/admin/predictions/match/{id}`
- Les drapeaux doivent s'afficher

### 4. Vérifier les données
```bash
docker exec -it landingpagecan-laravel.test-1 php artisan tinker

# Compter
>>> App\Models\Team::count()              # = 8
>>> App\Models\Bar::count()               # = nombre de PDV dans CSV
>>> App\Models\MatchGame::count()         # = nombre de matchs

# Vérifier les ISO codes
>>> App\Models\Team::whereNull('iso_code')->count()  # = 0 (tous ont un iso_code)

# Lister
>>> App\Models\Team::orderBy('name')->get(['name', 'iso_code'])
```

---

## 🔧 Commandes Utiles

```bash
# Entrer dans Docker
docker exec -it landingpagecan-laravel.test-1 bash

# Nettoyer les caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Voir les logs
tail -f storage/logs/laravel.log

# Vérifier les migrations
php artisan migrate:status

# Lister les seeders disponibles
ls -la database/seeders/
```

---

## 📊 Résumé des Changements

### Fichiers Modifiés (Drapeaux)
- ✅ `resources/views/matches.blade.php`
- ✅ `resources/views/admin/match-predictions.blade.php`
- ✅ `resources/views/admin/matches.blade.php`
- ✅ `resources/views/components/match-card.blade.php`
- ✅ `resources/views/components/team-flag.blade.php`
- ✅ `resources/views/admin/teams.blade.php`
- ✅ `resources/views/admin/predictions.blade.php`
- ✅ `resources/views/admin/phase-matches.blade.php`
- ✅ `resources/views/admin/match-venue-matrix.blade.php`
- ✅ `resources/views/admin/dashboard.blade.php`
- ✅ `resources/views/admin/calendar.blade.php`
- ✅ `resources/views/admin/edit-team.blade.php`

### Fichiers Modifiés (Légende)
- ✅ `resources/views/map.blade.php` (ajout légende + arrondi carte)

### Fichiers Créés
- ✅ `database/seeders/TeamIsoCodesSeeder.php`
- ✅ `database/sql/add_team_iso_codes.sql`
- ✅ `FIX_TEAMS_GUIDE.md` (ce fichier)

---

## ⚠️ Important

1. **Le CSV est correct** - Il contient bien les 8 équipes
2. **Le seeder est correct** - Il lit bien le nouveau format CSV
3. **Le problème** - Les équipes doivent être réimportées dans la base
4. **La solution** - Exécuter les seeders dans Docker (voir ci-dessus)

---

## 🆘 Si Rien ne Fonctionne

1. Vérifier que le fichier `venues.csv` est bien à la racine du projet
2. Vérifier que Docker est lancé
3. Réinitialiser complètement:
   ```bash
   docker exec -it landingpagecan-laravel.test-1 bash
   php artisan migrate:fresh --force
   php artisan db:seed --class=FreshDeploymentSeeder --force
   php artisan cache:clear
   exit
   ```
4. Tester immédiatement sur `/matches`

---

**Date:** 19 Décembre 2025
**Status:** ✅ Corrections appliquées, seeders prêts
**Action requise:** Exécuter les seeders dans Docker
