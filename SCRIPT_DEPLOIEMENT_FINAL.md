# 🚀 Script de Déploiement Final - Production CAN 2025

## ⚠️ IMPORTANT: Nettoyage Préalable

Avant de déployer, il faut nettoyer les anciennes données incohérentes:

### Script Forge de Déploiement Complet

```bash
#!/bin/bash

$CREATE_RELEASE()

cd $FORGE_RELEASE_DIRECTORY

# Installation dépendances
$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Frontend
npm ci
npm run build

# ==========================================
# MIGRATIONS
# ==========================================
echo "🔄 Running migrations..."
$FORGE_PHP artisan migrate --force

# ==========================================
# NETTOYAGE DES ANCIENNES DONNÉES
# ==========================================
echo "🧹 Cleaning old invalid animations..."
$FORGE_PHP artisan tinker --execute="
    \$invalidAnimations = \App\Models\Animation::whereNotExists(function(\$query) {
        \$query->select(\Illuminate\Support\Facades\DB::raw(1))
              ->from('matches')
              ->whereColumn('matches.id', 'animations.match_id');
    })->delete();
    echo 'Deleted ' . \$invalidAnimations . ' invalid animations' . PHP_EOL;
"

# ==========================================
# SEEDERS DANS L'ORDRE
# ==========================================
echo "🌍 Seeding Teams (24 équipes)..."
$FORGE_PHP artisan db:seed --class=TeamSeeder --force

echo "🏟️ Seeding Stadiums (6 stades)..."
$FORGE_PHP artisan db:seed --class=StadiumSeeder --force

echo "⚽ Seeding Matches (25+ matchs)..."
$FORGE_PHP artisan db:seed --class=MatchSeeder --force

echo "📍 Fixing Venues & Animations (60 PDV + 62 animations)..."
$FORGE_PHP artisan db:seed --class=FixAnimationsSeeder --force

# ==========================================
# OPTIMISATIONS
# ==========================================
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

## 📊 Résultats Attendus

Après déploiement, vous devriez avoir:

| Ressource | Quantité | Description |
|-----------|----------|-------------|
| **Teams** | 24 | Équipes nationales africaines |
| **Stadiums** | 6+ | Stades de la CAN |
| **Matches** | 25+ | Matchs de poules + knockout |
| **Venues** | 60 | Points de vente avec coordonnées |
| **Animations** | 62 | Liens venue-match valides |

---

## 🧪 Test Local Final

Avant de déployer en production:

```bash
# Nettoyer les animations invalides
docker exec -w /app landingpagecan-laravel.test-1 php artisan tinker --execute="
    \$deleted = \App\Models\Animation::whereNotExists(function(\$query) {
        \$query->select(\Illuminate\Support\Facades\DB::raw(1))
              ->from('matches')
              ->whereColumn('matches.id', 'animations.match_id');
    })->delete();
    echo 'Deleted ' . \$deleted . ' invalid animations' . PHP_EOL;
"

# Exécuter tous les seeders
docker exec -w /app landingpagecan-laravel.test-1 php artisan db:seed --class=TeamSeeder
docker exec -w /app landingpagecan-laravel.test-1 php artisan db:seed --class=StadiumSeeder
docker exec -w /app landingpagecan-laravel.test-1 php artisan db:seed --class=MatchSeeder
docker exec -w /app landingpagecan-laravel.test-1 php artisan db:seed --class=FixAnimationsSeeder

# Vérifier
docker exec -w /app landingpagecan-laravel.test-1 php artisan tinker --execute="
    echo '=== FINAL CHECK ===' . PHP_EOL;
    echo 'Teams: ' . \App\Models\Team::count() . PHP_EOL;
    echo 'Stadiums: ' . \App\Models\Stadium::count() . PHP_EOL;
    echo 'Matches: ' . \App\Models\MatchGame::count() . PHP_EOL;
    echo 'Venues: ' . \App\Models\Bar::count() . PHP_EOL;
    echo 'Valid Animations: ' . \App\Models\Animation::whereExists(function(\$q) {
        \$q->select(\Illuminate\Support\Facades\DB::raw(1))
          ->from('matches')
          ->whereColumn('matches.id', 'animations.match_id');
    })->count() . PHP_EOL;
"
```

---

## ✅ Checklist Finale de Déploiement

### Avant le Déploiement
- [ ] Backup de la base de données production créé
- [ ] Script de déploiement Forge mis à jour avec nettoyage
- [ ] Tests locaux effectués et validés
- [ ] Commit et push vers Git

### Pendant le Déploiement
- [ ] Logs Forge surveillés en temps réel
- [ ] Aucune erreur dans les migrations
- [ ] Aucune erreur dans les seeders
- [ ] Nettoyage des animations invalides confirmé

### Après le Déploiement
- [ ] Vérification SSH des données (tinker)
- [ ] Test de la carte des venues (60 points)
- [ ] Test de la liste des matches (25+ matches)
- [ ] Test des animations (liens venue-match)
- [ ] Vérification que les users et predictions sont intacts

---

## 🎯 Commandes de Vérification Post-Déploiement

```bash
ssh forge@votresite.com
cd /home/forge/votresite.com

php artisan tinker --execute="
    echo '╔════════════════════════════════════════╗' . PHP_EOL;
    echo '║   PRODUCTION DATA VERIFICATION        ║' . PHP_EOL;
    echo '╚════════════════════════════════════════╝' . PHP_EOL . PHP_EOL;

    echo '📊 STATISTICS:' . PHP_EOL;
    echo '  Teams: ' . \App\Models\Team::count() . ' (expected: 24)' . PHP_EOL;
    echo '  Stadiums: ' . \App\Models\Stadium::count() . ' (expected: 6+)' . PHP_EOL;
    echo '  Matches: ' . \App\Models\MatchGame::count() . ' (expected: 25+)' . PHP_EOL;
    echo '  Venues: ' . \App\Models\Bar::count() . ' (expected: 60)' . PHP_EOL;
    echo '  Venues with coords: ' . \App\Models\Bar::whereNotNull('latitude')->count() . ' (expected: 60)' . PHP_EOL;
    echo '  Venues with zones: ' . \App\Models\Bar::whereNotNull('zone')->count() . ' (expected: 60)' . PHP_EOL;

    \$validAnimations = \App\Models\Animation::whereExists(function(\$q) {
        \$q->select(\Illuminate\Support\Facades\DB::raw(1))
          ->from('matches')
          ->whereColumn('matches.id', 'animations.match_id');
    })->count();

    echo '  Valid Animations: ' . \$validAnimations . ' (expected: 62)' . PHP_EOL . PHP_EOL;

    echo '👥 USER DATA (should remain unchanged):' . PHP_EOL;
    echo '  Users: ' . \App\Models\User::count() . PHP_EOL;
    echo '  Predictions: ' . \App\Models\Prediction::count() . PHP_EOL . PHP_EOL;

    echo '✅ SAMPLE DATA:' . PHP_EOL;
    \$animation = \App\Models\Animation::with(['bar', 'match'])->whereHas('match')->first();
    if (\$animation) {
        echo '  Sample: ' . \$animation->bar->name . ' (' . \$animation->bar->zone . ')' . PHP_EOL;
        echo '    → ' . \$animation->match->team_a . ' vs ' . \$animation->match->team_b . PHP_EOL;
        echo '    → Date: ' . \$animation->animation_date . ' ' . \$animation->animation_time . PHP_EOL;
        echo '    → Coords: ' . \$animation->bar->latitude . ', ' . \$animation->bar->longitude . PHP_EOL;
    }
"
```

---

## 🚨 Si Erreur Pendant le Déploiement

### Rollback Rapide

```bash
# SSH vers production
ssh forge@votresite.com
cd /home/forge/votresite.com

# Restaurer la DB depuis le backup
mysql -u forge -p nom_database < backup_YYYYMMDD_HHMMSS.sql

# Revenir au commit précédent
git reset --hard COMMIT_PRECEDENT
composer install --no-dev
npm ci && npm run build
php artisan optimize
```

---

## 📝 Commit Message Recommandé

```bash
git add database/seeders/FixAnimationsSeeder.php \
        forge-deployment-script.sh \
        GUIDE_DEPLOIEMENT_PRODUCTION.md \
        SCRIPT_DEPLOIEMENT_FINAL.md

git commit -m "feat: Production deployment - 60 venues + matches + animations

🎯 Deployment Components:
- TeamSeeder: 24 équipes nationales
- StadiumSeeder: 6+ stades CAN
- MatchSeeder: 25+ matchs (poules + knockout)
- FixAnimationsSeeder: 60 venues avec coordonnées OSM + 62 animations

✨ Features:
- Auto-creation of missing venues
- Improved team name matching (RDC vs RD Congo, Sénégal vs SENEGAL)
- Clean invalid animations before seeding
- Idempotent seeders (safe to re-run)

🔒 Safety:
- No user data affected
- No prediction data lost
- DB transactions with rollback
- Old invalid animations cleaned up

📊 Expected Production State:
- 60 venues with valid coordinates (Dakar region)
- All venues with zones assigned
- 62 venue-match links (animations)
- Geolocation map fully functional

See SCRIPT_DEPLOIEMENT_FINAL.md for deployment instructions."

git push origin main
```

---

## 🎊 Success Criteria

Déploiement réussi si:

✅ Aucune erreur dans les logs Forge
✅ 60 venues créés avec coordonnées
✅ 25+ matches créés
✅ 62 animations valides
✅ Users et predictions intacts
✅ Carte de géolocalisation fonctionnelle
✅ Pas de points dans l'océan

**Vous êtes prêt à déployer! 🚀**
