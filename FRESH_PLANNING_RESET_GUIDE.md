# Guide: Fresh Planning Reset & Sync - GAZELLE

## ⚠️ ATTENTION: OPÉRATION DESTRUCTIVE

Ce seeder supprime **TOUTES** les données existantes des tables suivantes:
- `animations` (liens match-venue)
- `predictions` (tous les pronostics utilisateurs)
- `matches` (tous les matchs)
- `teams` (toutes les équipes)
- `bars` (tous les PDV)

---

## 🎯 Objectif

Synchroniser complètement la base de données de production avec le planning CSV fourni en effectuant:
1. **Reset complet** de toutes les données de matchs/équipes/venues
2. **Import frais** depuis les données CSV
3. **Recréation des liens** match-venue (animations)

---

## 📋 Utilisation

### **Commande:**

```bash
php artisan db:seed --class=FreshPlanningSeeder
```

### **Processus:**

1. **Confirmation requise** - Le seeder demande confirmation avant de procéder
2. **Nettoyage** - Truncate de toutes les tables concernées
3. **Import** - Création des teams, venues, matches, animations
4. **Résumé** - Affichage du nombre d'enregistrements créés

---

## 🔄 Processus Détaillé

### **Étape 1: Nettoyage (Destructive)**

```sql
-- Foreign keys désactivées temporairement
TRUNCATE TABLE animations;
TRUNCATE TABLE predictions;
TRUNCATE TABLE matches;
TRUNCATE TABLE teams;
TRUNCATE TABLE bars;
-- Foreign keys réactivées
```

**⚠️ Toutes les données de ces tables sont PERDUES!**

---

### **Étape 2: Import Teams**

**Règles:**
- Extraction de tous les noms d'équipes du CSV (`team_1` et `team_2`)
- Création unique (pas de doublons)
- Si `team_2` est vide, `team_1` contient le nom du match playoff (ex: "FINALE")

**Exemples:**
```
team_1: SENEGAL, team_2: BOTSWANA     → 2 équipes créées
team_1: HUITIEME DE FINALE, team_2:    → 1 "équipe" (nom de match)
```

**Output:**
```
👥 Importing teams...
   ✓ Created/verified 15 teams
```

---

### **Étape 3: Import Venues (PDV)**

**Règles:**
- Trim de tous les strings (nom, zone)
- Clé unique: `nom + zone` (même nom dans 2 zones = 2 PDV différents)
- `type_pdv` par défaut: `dakar` si vide dans CSV
- `address` = `zone`
- `is_active` = `true`

**Données extraites:**
```csv
venue_name, zone, latitude, longitude, TYPE_PDV
CHEZ JEAN, THIAROYE, 14.7517342, -17.381228, dakar
```

**Devient:**
```php
[
    'name' => 'CHEZ JEAN',
    'zone' => 'THIAROYE',
    'address' => 'THIAROYE',
    'latitude' => 14.7517342,
    'longitude' => -17.381228,
    'type_pdv' => 'dakar',
    'is_active' => true,
]
```

**Output:**
```
🏢 Importing venues...
   ✓ Created/verified 78 venues
```

---

### **Étape 4: Import Matches**

**Parsing Date/Time:**
```
CSV: date="23/12/2025", time="15 H"
→ 2025-12-23 15:00:00
```

**Deux Types de Matchs:**

#### **A. Matchs Normaux (avec 2 équipes)**
```csv
23/12/2025, 15 H, SENEGAL, BOTSWANA
```

**Devient:**
```php
[
    'match_date' => '2025-12-23 15:00:00',
    'team_a' => 'SENEGAL',
    'team_b' => 'BOTSWANA',
    'home_team_id' => <team_id>,
    'away_team_id' => <team_id>,
    'phase' => 'group_stage',
    'status' => 'scheduled',
]
```

#### **B. Matchs Playoffs (sans team_2)**
```csv
03/01/2026, 16 H, HUITIEME DE FINALE, (vide)
```

**Devient:**
```php
[
    'match_date' => '2026-01-03 16:00:00',
    'match_name' => 'HUITIEME DE FINALE',
    'team_a' => 'TBD',
    'team_b' => 'TBD',
    'home_team_id' => null,
    'away_team_id' => null,
    'phase' => 'round_of_16',
    'status' => 'scheduled',
]
```

**Détection Automatique Phase:**
```php
'HUITIEME DE FINALE'    → phase: 'round_of_16'
'QUART DE FINALE'       → phase: 'quarter_final'
'DEMI FINALE'           → phase: 'semi_final'
'TROISIEME PLACE'       → phase: 'third_place'
'FINALE'                → phase: 'final'
```

**Output:**
```
⚽ Importing matches...
   ✓ Created/verified 45 matches
```

---

### **Étape 5: Import Animations (Liens Match-Venue)**

**Processus:**
1. Pour chaque ligne CSV, trouver le match correspondant
2. Trouver le venue correspondant
3. Créer le lien (animation) entre les deux

**Exemple:**
```csv
CHEZ JEAN, THIAROYE, 23/12/2025, 15 H, SENEGAL, BOTSWANA
```

**Crée:**
```php
Animation::create([
    'match_id' => <id du match SENEGAL vs BOTSWANA à 15h>,
    'bar_id' => <id du venue CHEZ JEAN - THIAROYE>,
    'animation_date' => '2025-12-23',
    'animation_time' => '15:00',
    'is_active' => true,
]);
```

**Output:**
```
🔗 Importing animations (match-venue links)...
   ✓ Created 450 animations
   ⚠ 2 errors during animation import
```

---

## 📊 Résumé Final

```
📊 Summary:
   - Teams: 15
   - Venues: 78
   - Matches: 45
   - Animations: 450
```

---

## 🛡️ Sécurités Implémentées

### **1. Confirmation Obligatoire**

```bash
⚠️  WARNING: This will DELETE ALL existing data!
Tables affected: animations, matches, teams, bars
Do you want to continue? (yes/no) [no]:
```

**→ L'utilisateur DOIT taper "yes" pour continuer**

### **2. Foreign Keys**

```php
Schema::disableForeignKeyConstraints();
// ... truncate tables ...
Schema::enableForeignKeyConstraints();
```

**→ Évite les erreurs de contraintes**

### **3. Trim Automatique**

```php
'venue_name' => trim($row[0]),
'zone' => trim($row[1]),
// ...tous les champs
```

**→ Pas d'espaces parasites**

### **4. Déduplication**

- **Teams:** Nom unique
- **Venues:** Nom + Zone unique
- **Matches:** Date + Teams unique
- **Animations:** Match + Venue unique

---

## 🧪 Test en Environnement Local

### **Avant Production:**

```bash
# 1. Backup de la DB
php artisan db:backup  # Ou via mysqldump

# 2. Test du seeder
php artisan db:seed --class=FreshPlanningSeeder

# 3. Vérification
php artisan tinker
>>> \App\Models\MatchGame::count()
>>> \App\Models\Team::count()
>>> \App\Models\Bar::count()
>>> \App\Models\Animation::count()

# 4. Vérifier un match specifique
>>> \App\Models\MatchGame::with('animations.bar')->first()
```

---

## ⚠️ Précautions Production

### **AVANT d'exécuter:**

1. ✅ **Backup complet de la base de données**
   ```bash
   mysqldump -u user -p database > backup_$(date +%Y%m%d_%H%M%S).sql
   ```

2. ✅ **Notifier les utilisateurs** (maintenance)

3. ✅ **Vérifier que personne n'est en train de jouer**

4. ✅ **Tester sur une copie de la DB de production**

### **PENDANT l'exécution:**

5. ✅ **Mode maintenance**
   ```bash
   php artisan down
   ```

6. ✅ **Exécuter le seeder**
   ```bash
   php artisan db:seed --class=FreshPlanningSeeder
   ```

7. ✅ **Vérifications**
   - Nombre de matchs correct
   - Nombre de venues correct
   - Animations créées
   - Dates correctes

### **APRÈS l'exécution:**

8. ✅ **Tests fonctionnels**
   - Affichage des matchs
   - Pronostics possibles
   - Map des venues

9. ✅ **Sortir du mode maintenance**
   ```bash
   php artisan up
   ```

---

## 📝 Données CSV Intégrées

Le seeder contient **toutes les 80 lignes du CSV** dans la méthode `getCsvContent()`.

**Modification:**
Si vous devez modifier les données, éditez directement le fichier:
```php
protected function getCsvContent(): array
{
    return [
        ['CHEZ JEAN', 'THIAROYE', '23/12/2025', '15 H', 'SENEGAL', 'BOTSWANA', '14.7517342', '-17.381228', ''],
        // ... ajoutez/modifiez les lignes ici
    ];
}
```

---

## 🔧 Personnalisation

### **Ajouter un TYPE_PDV:**

```php
// Dans importVenues()
'type_pdv' => empty($row['type_pdv']) ? 'dakar' : $row['type_pdv'],
```

### **Modifier le Parsing de Time:**

```php
// Dans parseDateTime()
// Actuellement: "15 H" → 15:00
// Pour supporter "15h30":
$timeParts = explode(':', str_replace([' H', ' h', 'H', 'h'], ':', $time));
$hour = (int) $timeParts[0];
$minute = (int) ($timeParts[1] ?? 0);
```

### **Ajouter des Validations:**

```php
// Dans importVenues()
if (empty($venueData['latitude']) || empty($venueData['longitude'])) {
    $this->command->warn("⚠ Skipping venue without coordinates: {$venueData['name']}");
    continue;
}
```

---

## 🐛 Troubleshooting

### **Erreur: "Foreign key constraint fails"**

**Cause:** Foreign keys non désactivées

**Solution:**
```php
// Vérifier dans le seeder
Schema::disableForeignKeyConstraints();
// ... operations ...
Schema::enableForeignKeyConstraints();
```

---

### **Erreur: "Animation could not be linked"**

**Cause:** Match ou Venue non trouvé

**Action:**
1. Vérifier les données CSV (trim, typos)
2. Regarder les warnings du seeder
3. Vérifier manuellement:
   ```php
   MatchGame::where('team_a', 'SENEGAL')->get()
   Bar::where('name', 'CHEZ JEAN')->get()
   ```

---

### **Dates Incorrectes**

**Cause:** Format date mal parsé

**Vérification:**
```php
// Dans parseDateTime()
dd($date, $time, $result);
```

---

## ✅ Checklist Post-Import

- [ ] Nombre de teams correct (environ 15)
- [ ] Nombre de venues correct (environ 78)
- [ ] Nombre de matchs correct (environ 45)
- [ ] Nombre d'animations correct (environ 450)
- [ ] Dates des matchs correctes (23/12/2025 → 18/01/2026)
- [ ] Phases détectées correctement (group_stage, playoffs)
- [ ] Venues avec coordonnées GPS
- [ ] Aucune animation orpheline
- [ ] Page /matches fonctionne
- [ ] Page /map affiche tous les PDV
- [ ] Pronostics possibles sur les matchs

---

## 📚 Fichiers Liés

- **Seeder:** `database/seeders/FreshPlanningSeeder.php`
- **Models:** `app/Models/{MatchGame, Team, Bar, Animation}.php`
- **Config:** `config/game.php`

---

## 🚀 Commande Complète (Production)

```bash
# 1. Backup
mysqldump -u root -p gazelle > backup_$(date +%Y%m%d_%H%M%S).sql

# 2. Maintenance
php artisan down --message="Mise à jour du calendrier" --retry=60

# 3. Import
php artisan db:seed --class=FreshPlanningSeeder

# 4. Vérif rapide
php artisan tinker
>>> \App\Models\MatchGame::count()
>>> exit

# 5. Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# 6. Up
php artisan up
```

---

**Créé:** 19 Décembre 2024  
**Auteur:** Big Five Abidjan  
**Projet:** GAZELLE - Fresh Planning Sync  
**Version:** 1.0
