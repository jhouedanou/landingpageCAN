# 🚀 Déploiement du FixAnimationsSeeder

## Contexte
Ce seeder corrige deux problèmes critiques de production:
1. **Venues (Bars)**: Coordonnées incorrectes (points dans l'océan) et zones manquantes
2. **Animations**: Table pivot vide ou incorrecte liant les Venues aux Matches

## Solution Technique

### 1. Logique de mise à jour des Venues
```php
// Trouve le venue par nom (après trim)
$venue = Bar::where('name', trim($venueName))->first();

// Met à jour: zone, latitude, longitude
$venue->update([
    'zone' => $item['zone'],
    'latitude' => $item['latitude'],
    'longitude' => $item['longitude'],
]);
```

### 2. Parsing des dates et heures
**Format JSON:**
- Date: `"12-23-25"` = MM-DD-YY (23 décembre 2025)
- Heure: `"15 H"` = HH H (15:00:00)

**Conversion Carbon:**
```php
$date = Carbon::createFromFormat('m-d-y', $item['date']); // "12-23-25"
$hour = (int) explode(' ', $item['time'])[0];            // "15 H" -> 15
$datetime = $date->setTime($hour, 0, 0);                 // 2025-12-23 15:00:00
```

### 3. Logique de matching des Matches

**Cas 1: Matches de phase éliminatoire**
```php
$phaseMap = [
    'HUITIEME DE FINALE' => 'round_of_16',
    'QUART DE FINALE' => 'quarter_final',
    'DEMI FINALE' => 'semi_final',
    'TROISIEME PLACE' => 'third_place',
    'FINALE' => 'final',
];

$match = MatchGame::where('phase', $phaseMap[$matchName])->first();
```

**Cas 2: Matches réguliers avec équipes**
```php
// "SENEGAL VS BOTSWANA" -> team_a = "SENEGAL", team_b = "BOTSWANA"
$teams = explode(' VS ', strtoupper($matchName));
$teamA = trim($teams[0]);
$teamB = trim($teams[1]);

// Recherche case-insensitive dans les deux sens (A vs B ou B vs A)
$match = MatchGame::where(function($query) use ($teamA, $teamB) {
    $query->whereRaw('UPPER(TRIM(team_a)) = ?', [$teamA])
          ->whereRaw('UPPER(TRIM(team_b)) = ?', [$teamB]);
})
->orWhere(function($query) use ($teamA, $teamB) {
    $query->whereRaw('UPPER(TRIM(team_a)) = ?', [$teamB])
          ->whereRaw('UPPER(TRIM(team_b)) = ?', [$teamA]);
})
->first();
```

### 4. Création/Mise à jour du Pivot (Animation)
```php
Animation::updateOrCreate(
    [
        'bar_id' => $venue->id,
        'match_id' => $match->id,
    ],
    [
        'animation_date' => $datetime->format('Y-m-d'),
        'animation_time' => $datetime->format('H:i:s'),
        'is_active' => true,
    ]
);
```

## 🧪 Test Local

### Étape 1: Tester le seeder localement
```bash
# Via Docker
docker exec -w /app landingpagecan-laravel.test-1 php artisan db:seed --class=FixAnimationsSeeder

# Ou si PHP est disponible localement
php artisan db:seed --class=FixAnimationsSeeder
```

### Étape 2: Vérifier les résultats
```bash
# Vérifier les venues mises à jour
docker exec -w /app landingpagecan-laravel.test-1 php artisan tinker
>>> \App\Models\Bar::whereNotNull('latitude')->count();
>>> \App\Models\Bar::where('name', 'CHEZ JEAN')->first();

# Vérifier les animations créées
>>> \App\Models\Animation::count();
>>> \App\Models\Animation::with(['bar', 'match'])->take(5)->get();
```

### Étape 3: Vérifier l'absence d'erreurs
Le seeder affichera:
- ✅ Nombre de venues mis à jour
- ✅ Nombre d'animations créées/mises à jour
- ⚠️ Liste des venues non trouvées (si applicable)
- ⚠️ Liste des matches non trouvés (si applicable)

## 🚀 Déploiement Production (Laravel Forge)

### ⚠️ IMPORTANT: Modification du script de déploiement

**Problème actuel:**
Le script Forge actuel exécute `php artisan migrate --force --seed`, ce qui:
- ✅ Exécute les migrations (OK)
- ❌ Exécute TOUS les seeders, ce qui peut réinitialiser les utilisateurs et autres données

**Solution:**
Modifier temporairement le script de déploiement Forge pour exécuter uniquement le seeder de correction.

### Script Forge à utiliser pour ce déploiement

```bash
cd /home/forge/votresite.com

git pull origin $FORGE_SITE_BRANCH

$FORGE_COMPOSER install --no-interaction --prefer-dist --optimize-autoloader --no-dev

( flock -w 10 9 || exit 1
    echo 'Restarting FPM...'; sudo -S service $FORGE_PHP_FPM reload ) 9>/tmp/fpmlock

if [ -f artisan ]; then
    # Migration normale
    $FORGE_PHP artisan migrate --force

    # 🔥 SEEDER SPÉCIFIQUE - NE PAS EXÉCUTER TOUS LES SEEDERS
    $FORGE_PHP artisan db:seed --class=FixAnimationsSeeder --force

    # Clear caches
    $FORGE_PHP artisan config:clear
    $FORGE_PHP artisan cache:clear
    $FORGE_PHP artisan view:clear
fi

$FORGE_NPM ci
$FORGE_NPM run build
```

### Étapes de déploiement

1. **Push le seeder vers le repository**
```bash
git add database/seeders/FixAnimationsSeeder.php
git commit -m "feat: Add FixAnimationsSeeder to fix venue coordinates and animations"
git push origin main
```

2. **Modifier le script de déploiement Forge**
   - Aller dans Laravel Forge
   - Sélectionner le site
   - Onglet "Deployment Script"
   - Remplacer le script par celui ci-dessus
   - Sauvegarder

3. **Déployer**
   - Cliquer sur "Deploy Now" dans Forge
   - OU faire un push vers la branche configurée

4. **Vérifier les logs**
   - Aller dans "Recent Deployments" dans Forge
   - Vérifier que le seeder s'est exécuté sans erreur
   - Chercher les messages: "✅ FixAnimationsSeeder completed successfully!"

5. **IMPORTANT: Restaurer le script de déploiement**
   Après le déploiement réussi, restaurer le script original:
```bash
# ... existing script ...
if [ -f artisan ]; then
    $FORGE_PHP artisan migrate --force
    # ❌ NE PAS LAISSER --seed ici pour les futurs déploiements
    # $FORGE_PHP artisan migrate --force --seed

    $FORGE_PHP artisan config:clear
    $FORGE_PHP artisan cache:clear
    $FORGE_PHP artisan view:clear
fi
# ... rest of script ...
```

## 📊 Résultat attendu

Après l'exécution du seeder:

### Venues (Table `bars`)
- ✅ Toutes les coordonnées corrigées (plus de points dans l'océan)
- ✅ Zones correctement assignées
- ✅ Exemple: "CHEZ JEAN" aura zone="THIAROYE", lat=14.751734, lng=-17.381228

### Animations (Table `animations`)
- ✅ Chaque venue sera liée aux matches corrects
- ✅ Dates et heures correctement formatées
- ✅ Exemple: "CHEZ JEAN" sera lié au match "SENEGAL VS BOTSWANA" le 23/12/2025 à 15h

### Vérification Post-Déploiement

```bash
# SSH vers le serveur de production
ssh forge@votresite.com

cd /home/forge/votresite.com

# Vérifier les animations
php artisan tinker
>>> \App\Models\Animation::count();
>>> \App\Models\Bar::whereNotNull('latitude')->whereNotNull('zone')->count();

# Vérifier une animation spécifique
>>> $animation = \App\Models\Animation::with(['bar', 'match'])->first();
>>> echo $animation->bar->name . ' - ' . $animation->match->team_a . ' vs ' . $animation->match->team_b;
```

## 🔄 Rollback (si nécessaire)

Si le seeder cause des problèmes:

```bash
# SSH vers production
ssh forge@votresite.com
cd /home/forge/votresite.com

# Restaurer depuis une sauvegarde de base de données
# OU supprimer toutes les animations et re-exécuter
php artisan tinker
>>> \App\Models\Animation::truncate();
>>> exit

# Re-exécuter le seeder si nécessaire
php artisan db:seed --class=FixAnimationsSeeder --force
```

## 📝 Notes Techniques

### Gestion des transactions
Le seeder utilise `DB::beginTransaction()` et `DB::commit()` pour garantir l'atomicité:
- Si une erreur survient, toutes les modifications sont annulées (`DB::rollBack()`)
- Les données restent cohérentes

### Gestion des doublons
La méthode `updateOrCreate()` évite les doublons:
- Si une animation existe déjà pour (bar_id, match_id), elle est mise à jour
- Sinon, elle est créée

### Logging
Le seeder affiche:
- Nombre de venues mis à jour
- Nombre d'animations créées
- Liste des venues/matches non trouvés
- Messages d'erreur détaillés

## ✅ Checklist de déploiement

- [ ] Tester le seeder en local
- [ ] Vérifier les résultats en local
- [ ] Commit et push le seeder
- [ ] Modifier le script Forge pour utiliser le seeder spécifique
- [ ] Déployer via Forge
- [ ] Vérifier les logs de déploiement
- [ ] Vérifier les données en production (SSH + tinker)
- [ ] Restaurer le script Forge original
- [ ] Documenter les résultats

## 🆘 Support

En cas de problème:
1. Vérifier les logs Forge
2. SSH vers le serveur et vérifier les logs Laravel (`storage/logs/laravel.log`)
3. Exécuter le seeder manuellement avec `--verbose` pour plus de détails
