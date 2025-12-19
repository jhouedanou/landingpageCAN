# 🚀 Guide de Déploiement Production - CAN 2025

## 📋 État Actuel vs État Cible

### Production Actuelle ❌
- 20 venues (au lieu de 60)
- Sans zones
- Sans coordonnées
- Sans matches
- Aucune animation

### Production Cible ✅
- **60 venues** avec zones et coordonnées validées
- **48 équipes nationales** africaines
- **10+ stades** de la CAN
- **52 matches** (phase de poules + phases éliminatoires)
- **62+ animations** (liens venue-match)

---

## 🔄 Seeders Utilisés

### 1. **TeamSeeder**
Crée les 48 équipes nationales africaines (Sénégal, RDC, Côte d'Ivoire, etc.)

**Mode:** `updateOrCreate` (idempotent)
- Si l'équipe existe → mise à jour
- Si l'équipe n'existe pas → création

**Tables affectées:**
- `teams`

### 2. **StadiumSeeder**
Crée les stades de la CAN (Olembe, Alassane Ouattara, etc.)

**Mode:** `updateOrCreate` (idempotent)
- Si le stade existe → mise à jour
- Si le stade n'existe pas → création

**Tables affectées:**
- `stadiums`

### 3. **MatchSeeder**
Crée les 52 matches de la CAN 2025:
- Phase de poules (6 groupes x 6 matches = 36 matches)
- 1/8e de finale (8 matches)
- Quarts de finale (4 matches)
- Demi-finales (2 matches)
- Finale + 3e place (2 matches)

**Mode:** `firstOrCreate` (idempotent)
- Si le match existe déjà → skip
- Sinon → création

**Tables affectées:**
- `matches`

### 4. **FixAnimationsSeeder** 🌟
**LE SEEDER PRINCIPAL** qui:
1. Crée/met à jour les **60 venues** avec coordonnées OSM validées
2. Assigne les zones géographiques
3. Lie les venues aux matches via des animations

**Mode:** `updateOrCreate` (idempotent)
- Venues: création si manquant, mise à jour sinon
- Animations: mise à jour ou création

**Tables affectées:**
- `bars` (venues)
- `animations` (pivot venue-match)

---

## 📝 Script de Déploiement Forge

### Script Complet
Copiez ce script dans Laravel Forge → Site → Deployment Script:

```bash
#!/bin/bash

$CREATE_RELEASE()

cd $FORGE_RELEASE_DIRECTORY

# Installation des dépendances PHP
$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Installation et build du frontend
npm ci
npm run build

# MIGRATIONS (⚠️ SANS --seed pour éviter de réinitialiser les users)
echo "🔄 Running migrations..."
$FORGE_PHP artisan migrate --force

# SEEDERS SPÉCIFIQUES (dans l'ordre!)
echo "🌍 Seeding Teams..."
$FORGE_PHP artisan db:seed --class=TeamSeeder --force

echo "🏟️ Seeding Stadiums..."
$FORGE_PHP artisan db:seed --class=StadiumSeeder --force

echo "⚽ Seeding Matches..."
$FORGE_PHP artisan db:seed --class=MatchSeeder --force

echo "📍 Fixing Venues & Animations (60 PDV)..."
$FORGE_PHP artisan db:seed --class=FixAnimationsSeeder --force

# Optimisations
echo "🔧 Optimizing..."
$FORGE_PHP artisan optimize
$FORGE_PHP artisan storage:link

# Clear caches
$FORGE_PHP artisan config:clear
$FORGE_PHP artisan cache:clear
$FORGE_PHP artisan view:clear
$FORGE_PHP artisan route:clear

$ACTIVATE_RELEASE()

$RESTART_QUEUES()

echo "✅ Deployment completed!"
```

---

## ⚠️ Points Critiques

### 1. NE PAS utiliser `migrate --seed`
```bash
# ❌ DANGEREUX - Réinitialise TOUT (users, predictions, etc.)
$FORGE_PHP artisan migrate --force --seed

# ✅ CORRECT - Migrations uniquement
$FORGE_PHP artisan migrate --force
```

### 2. Ordre des Seeders = Important
```bash
1. TeamSeeder     # Crée les équipes (requis pour MatchSeeder)
2. StadiumSeeder  # Crée les stades (requis pour MatchSeeder)
3. MatchSeeder    # Crée les matches (requis pour FixAnimationsSeeder)
4. FixAnimationsSeeder # Crée venues + lie aux matches
```

### 3. Idempotence
Tous les seeders peuvent être exécutés **plusieurs fois** sans problème:
- Pas de duplications
- Pas de réinitialisation des users
- Pas de perte de pronostics

---

## 🧪 Test en Local AVANT Production

### Étape 1: Backup de la DB locale
```bash
docker exec landingpagecan-mysql-1 mysqldump -u root -ppassword nom_db > backup_local.sql
```

### Étape 2: Test du déploiement
```bash
# Exécuter les seeders dans l'ordre
docker exec -w /app landingpagecan-laravel.test-1 php artisan db:seed --class=TeamSeeder
docker exec -w /app landingpagecan-laravel.test-1 php artisan db:seed --class=StadiumSeeder
docker exec -w /app landingpagecan-laravel.test-1 php artisan db:seed --class=MatchSeeder
docker exec -w /app landingpagecan-laravel.test-1 php artisan db:seed --class=FixAnimationsSeeder
```

### Étape 3: Vérifier les résultats
```bash
docker exec -w /app landingpagecan-laravel.test-1 php artisan tinker --execute="
echo 'Teams: ' . \App\Models\Team::count() . PHP_EOL;
echo 'Stadiums: ' . \App\Models\Stadium::count() . PHP_EOL;
echo 'Matches: ' . \App\Models\MatchGame::count() . PHP_EOL;
echo 'Venues: ' . \App\Models\Bar::count() . PHP_EOL;
echo 'Animations: ' . \App\Models\Animation::count() . PHP_EOL;
"
```

**Résultats attendus:**
```
Teams: 48
Stadiums: 10+
Matches: 52
Venues: 60
Animations: 62+
```

---

## 🚀 Déploiement Production - Étapes

### Étape 1: Backup Production
```bash
# SSH vers production
ssh forge@votresite.com

# Backup de la base de données
cd /home/forge/votresite.com
php artisan backup:run
# OU si pas de package backup:
mysqldump -u forge -p nom_database > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Étape 2: Mettre à jour le script Forge
1. Aller dans Laravel Forge
2. Sélectionner le site
3. Onglet "Deployment Script"
4. Coller le nouveau script (voir ci-dessus)
5. **Sauvegarder**

### Étape 3: Commit et Push
```bash
git add database/seeders/FixAnimationsSeeder.php \
        forge-deployment-script.sh \
        GUIDE_DEPLOIEMENT_PRODUCTION.md

git commit -m "feat: Production deployment with 60 venues and matches

🌟 Deployment includes:
- 48 teams (TeamSeeder)
- 10+ stadiums (StadiumSeeder)
- 52 matches (MatchSeeder)
- 60 venues with OSM coordinates (FixAnimationsSeeder)
- 62+ animations (venue-match links)

✅ Safe deployment:
- No user data reset
- No prediction data loss
- Idempotent seeders (can run multiple times)

📝 See GUIDE_DEPLOIEMENT_PRODUCTION.md for details"

git push origin main
```

### Étape 4: Déployer
**Option A: Déploiement automatique** (si configuré dans Forge)
- Le push déclenchera automatiquement le déploiement

**Option B: Déploiement manuel**
1. Aller dans Forge
2. Sélectionner le site
3. Cliquer sur "Deploy Now"

### Étape 5: Vérifier les logs
1. Dans Forge → Recent Deployments
2. Vérifier que tous les seeders se sont exécutés:
   ```
   ✅ Seeding Teams...
   ✅ Seeding Stadiums...
   ✅ Seeding Matches...
   ✅ Fixing Venues & Animations...
   ```

### Étape 6: Vérification Post-Déploiement
```bash
# SSH vers production
ssh forge@votresite.com
cd /home/forge/votresite.com

# Vérifier les données
php artisan tinker --execute="
echo '=== PRODUCTION DATA ===' . PHP_EOL;
echo 'Teams: ' . \App\Models\Team::count() . PHP_EOL;
echo 'Stadiums: ' . \App\Models\Stadium::count() . PHP_EOL;
echo 'Matches: ' . \App\Models\MatchGame::count() . PHP_EOL;
echo 'Venues: ' . \App\Models\Bar::count() . PHP_EOL;
echo 'Venues with coords: ' . \App\Models\Bar::whereNotNull('latitude')->count() . PHP_EOL;
echo 'Venues with zones: ' . \App\Models\Bar::whereNotNull('zone')->count() . PHP_EOL;
echo 'Animations: ' . \App\Models\Animation::count() . PHP_EOL;
echo 'Users: ' . \App\Models\User::count() . ' (should remain unchanged)' . PHP_EOL;
echo 'Predictions: ' . \App\Models\Prediction::count() . ' (should remain unchanged)' . PHP_EOL;
"
```

**Résultats attendus:**
```
=== PRODUCTION DATA ===
Teams: 48
Stadiums: 10+
Matches: 52
Venues: 60
Venues with coords: 60
Venues with zones: 60
Animations: 62+
Users: [nombre existant] (should remain unchanged)
Predictions: [nombre existant] (should remain unchanged)
```

---

## 🆘 Rollback (si problème)

### Si le déploiement échoue:

1. **Restaurer la base de données**
```bash
ssh forge@votresite.com
cd /home/forge/votresite.com
mysql -u forge -p nom_database < backup_YYYYMMDD_HHMMSS.sql
```

2. **Revenir au code précédent**
```bash
# Dans Forge, déployer le commit précédent
# OU en SSH:
cd /home/forge/votresite.com
git reset --hard COMMIT_PRECEDENT
composer install
php artisan optimize
```

### Si les seeders échouent partiellement:

```bash
# Re-exécuter uniquement les seeders qui ont échoué
ssh forge@votresite.com
cd /home/forge/votresite.com

# Par exemple, si seulement FixAnimationsSeeder a échoué:
php artisan db:seed --class=FixAnimationsSeeder --force
```

---

## 📊 Monitoring Post-Déploiement

### Vérifications à faire dans les 24h:

1. **Carte des venues**
   - Tester la page `/venues`
   - Vérifier que les 60 points sont affichés
   - Vérifier qu'aucun point n'est dans l'océan

2. **Liste des matches**
   - Tester la page admin des matches
   - Vérifier les 52 matches
   - Vérifier les animations (PDV assignés)

3. **Pronostics**
   - Vérifier que les utilisateurs peuvent toujours faire des pronostics
   - Vérifier que les pronostics existants n'ont pas été supprimés

4. **Logs d'erreurs**
```bash
ssh forge@votresite.com
tail -f /home/forge/votresite.com/storage/logs/laravel.log
```

---

## ✅ Checklist Finale

Avant de déployer en production:

- [ ] Backup de la base de données production créé
- [ ] Seeders testés en local avec succès
- [ ] Script de déploiement Forge mis à jour
- [ ] Commit et push effectués
- [ ] Plan de rollback documenté
- [ ] Monitoring préparé

Pendant le déploiement:

- [ ] Logs Forge vérifiés
- [ ] Aucune erreur dans les seeders
- [ ] Toutes les commandes exécutées avec succès

Après le déploiement:

- [ ] Vérification des données (tinker)
- [ ] Test de la carte des venues
- [ ] Test de la liste des matches
- [ ] Test des pronostics
- [ ] Logs d'erreurs vérifiés (pas d'erreurs critiques)

---

## 🎯 Résumé

Ce déploiement va transformer votre production de:
- ❌ 20 venues sans zones → ✅ 60 venues avec zones et coordonnées
- ❌ Pas de matches → ✅ 52 matches de la CAN 2025
- ❌ Pas d'animations → ✅ 62+ animations (liens venue-match)

**Sécurité:**
- Pas de perte de données utilisateurs
- Pas de perte de pronostics
- Seeders idempotents (peuvent être ré-exécutés)
- Transactions DB avec rollback automatique

**Prêt à déployer!** 🚀
