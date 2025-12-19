# Changements de Logique du Jeu - GAZELLE

## 🎯 Objectif

Rendre l'application **universellement accessible** tout en conservant le système de **bonus pour les check-ins** dans les points de vente partenaires.

---

## 📋 Résumé des Changements

### **Avant:**
❌ **Accès Restreint** - Les utilisateurs DEVAIENT être dans un PDV partenaire pour:
- Voir les matchs
- Faire des pronostics
- Consulter le calendrier

### **Après:**
✅ **Accès Universel** - Les utilisateurs peuvent:
- Voir les matchs depuis n'importe où
- Faire des pronostics depuis n'importe où
- Consulter le calendrier depuis n'importe où
- 🎁 **BONUS:** +4 points/jour en faisant un pronostic depuis un PDV partenaire

---

## 🔧 Modifications Techniques

### **1. Configuration (`config/game.php` - NOUVEAU)**

Nouveau fichier de configuration pour contrôler la logique du jeu:

```php
'require_venue_geofencing' => env('REQUIRE_VENUE_GEOFENCING', false),
'venue_bonus_points' => env('VENUE_BONUS_POINTS', 4),
'venue_proximity_radius' => env('VENUE_PROXIMITY_RADIUS_KM', 0.2),
```

**Variables d'environnement (`.env`):**
```env
# Game Logic Configuration
REQUIRE_VENUE_GEOFENCING=false    # false = accès universel
VENUE_BONUS_POINTS=4              # Points bonus PDV
VENUE_PROXIMITY_RADIUS_KM=0.2     # Rayon de détection (200m)
```

**Impact:**
- `REQUIRE_VENUE_GEOFENCING=false` → Les utilisateurs peuvent jouer partout
- `REQUIRE_VENUE_GEOFENCING=true` → Retour au comportement ancien (PDV obligatoire)

---

### **2. Web PredictionController**

**Fichier:** `app/Http/Controllers/Web/PredictionController.php`

#### **Changement: Validation**
```php
// AVANT
'venue_id' => 'required|exists:bars,id',

// APRÈS
'venue_id' => 'nullable|exists:bars,id', // Venue optionnel
```

#### **Changement: Logique de Vérification**
```php
// AVANT - Venue obligatoire
$venue = Bar::where('id', $request->venue_id)->where('is_active', true)->first();
if (!$venue) {
    return redirect()->route('venues')->with('error', 'PDV obligatoire');
}

// APRÈS - Venue optionnel (bonus uniquement)
$requireVenue = config('game.require_venue_geofencing', false);
$venue = null;

if ($request->venue_id) {
    $venue = Bar::where('id', $request->venue_id)->where('is_active', true)->first();
} elseif ($requireVenue) {
    return redirect()->route('venues')->with('error', 'PDV requis');
}
// Si !$requireVenue et !$venue → Pronostic autorisé sans PDV
```

#### **Changement: Attribution des Points**
```php
// AVANT - Points donnés automatiquement
$venuePointsAwarded = $this->pointsService->awardPredictionVenuePoints($user, $venue->id);

// APRÈS - Points donnés seulement si venue fourni (bonus optionnel)
$venuePointsAwarded = 0;
if ($venue) {
    $venuePointsAwarded = $this->pointsService->awardPredictionVenuePoints($user, $venue->id);
}
```

#### **Changement: Messages**
```php
// AVANT
'Pronostic enregistré ! (depuis ' . $venue->name . ')' // Crash si pas de venue

// APRÈS
$description = $match->team_a . ' ' . $request->score_a . ' - ' . $request->score_b . ' ' . $match->team_b;
if ($venue) {
    $description .= ' (depuis ' . $venue->name . ')';
}
if ($venuePointsAwarded > 0) {
    $description .= ' + ' . $venuePointsAwarded . ' pts venue bonus 🎉';
}
```

---

### **3. API PredictionController**

**Fichier:** `app/Http/Controllers/Api/PredictionController.php`

#### **Changement: Validation**
```php
// AVANT
'latitude' => 'required|numeric|between:-90,90',
'longitude' => 'required|numeric|between:-180,180',

// APRÈS
'latitude' => 'nullable|numeric|between:-90,90',
'longitude' => 'nullable|numeric|between:-180,180',
```

#### **Changement: Logique GPS**
```php
// AVANT - GPS obligatoire
$userLat = (float) $request->latitude;
$userLng = (float) $request->longitude;
$nearbyVenue = $this->geolocationService->findNearbyVenue($userLat, $userLng);

if (!$nearbyVenue) {
    return response()->json(['error' => 'PDV obligatoire'], 403);
}

// APRÈS - GPS optionnel
$requireVenue = config('game.require_venue_geofencing', false);
$nearbyVenue = null;

if ($request->latitude && $request->longitude) {
    $nearbyVenue = $this->geolocationService->findNearbyVenue(...);
}

if ($requireVenue && !$nearbyVenue) {
    return response()->json(['error' => 'PDV obligatoire'], 403);
}
// Si !$requireVenue → Pronostic autorisé sans GPS
```

#### **Changement: Points Bonus**
```php
// AVANT
$venuePointsAwarded = $this->pointsService->awardPredictionVenuePoints($user, $nearbyVenue->id);

// APRÈS
$venuePointsAwarded = 0;
if ($nearbyVenue) {
    $venuePointsAwarded = $this->pointsService->awardPredictionVenuePoints($user, $nearbyVenue->id);
}
```

---

### **4. Optimisation Performance**

#### **A. Cache Control (Layout)**

**Fichier:** `resources/views/components/layouts/app.blade.php`

```html
<!-- Meta tags pour meilleure gestion du cache -->
<meta http-equiv="Cache-Control" content="max-age=300, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
```

**Impact:**
- Cache de 5 minutes pour réduire les requêtes
- `must-revalidate` pour toujours avoir les dernières données
- Meilleure performance bouton "Retour"

#### **B. BFCache Optimization (Layout)**

**Amélioration du script de gestion du back button:**

```javascript
// Support bfcache (Back/Forward Cache)
window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        // Page restaurée depuis le cache navigateur
        console.log('[GAZELLE] Page restored from bfcache');
        hideLoader();
        
        // Refresh du contenu dynamique
        const pointsElements = document.querySelectorAll('[data-user-points]');
        if (pointsElements.length > 0 && window.userPointsCache) {
            pointsElements.forEach(el => el.textContent = window.userPointsCache);
        }
    }
});

// Préservation de l'état avant navigation
window.addEventListener('pagehide', () => {
    const pointsElement = document.querySelector('[data-user-points]');
    if (pointsElement) {
        window.userPointsCache = pointsElement.textContent;
    }
});
```

**Avantages:**
- ✅ Bouton retour **ultra-rapide** (pas de rechargement)
- ✅ Préservation de l'état (points, scroll position)
- ✅ Moins de consommation de données
- ✅ Meilleure UX mobile

---

## 📊 Système de Points

### **Points de Base (Inchangés)**

| Action | Points | Conditions |
|--------|--------|-----------|
| **Participation** | +1 pt | Pronostic enregistré |
| **Bon vainqueur** | +3 pts | Vainqueur correct |
| **Score exact** | +3 pts | Score exact |
| **Maximum/match** | 7 pts | 1 + 3 + 3 |

### **Points Bonus Venue (NOUVEAU)**

| Action | Points | Conditions |
|--------|--------|-----------|
| **Pronostic depuis PDV** | +4 pts | 1x par jour, dans rayon de 200m |

**Exemple:**
```
Utilisateur A (sans PDV):
- Pronostic score exact: 1 + 3 + 3 = 7 pts

Utilisateur B (depuis PDV):
- Pronostic score exact: 1 + 3 + 3 + 4 = 11 pts 🎉
```

---

## 🚀 Migration & Déploiement

### **1. Mise à Jour du `.env`**

Ajouter ces variables:
```env
REQUIRE_VENUE_GEOFENCING=false
VENUE_BONUS_POINTS=4
VENUE_PROXIMITY_RADIUS_KM=0.2
```

### **2. Clear Cache Laravel**

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### **3. Test de Régression**

**Sans PDV (nouveau comportement):**
```bash
# 1. Aller sur /matches
# 2. Faire un pronostic SANS sélectionner de PDV
# 3. Vérifier: Pronostic enregistré ✅
# 4. Points: +1 pt participation (pas de bonus)
```

**Avec PDV (bonus):**
```bash
# 1. Aller sur /map
# 2. Sélectionner un PDV
# 3. Faire un pronostic
# 4. Vérifier: +1 pt + 4 pts bonus = 5 pts minimum
```

**Avec geofencing activé:**
```bash
# Dans .env: REQUIRE_VENUE_GEOFENCING=true
# Tester qu'on NE PEUT PAS pronostiquer sans PDV
```

---

## 🎮 Impact Utilisateur

### **Avant:**

```
Utilisateur à domicile:
❌ Ne peut pas voir les matchs
❌ Ne peut pas pronostiquer
❌ Doit se déplacer au PDV

→ Expérience frustrante
```

### **Après:**

```
Utilisateur à domicile:
✅ Voit tous les matchs
✅ Peut pronostiquer librement
✅ Gagne des points (1 + jusqu'à 6)

Utilisateur au PDV:
✅ Voit tous les matchs
✅ Peut pronostiquer librement  
✅ Gagne des points (1 + jusqu'à 6) + BONUS +4 pts 🎉

→ Expérience fluide et incitative
```

---

## 📈 Bénéfices Business

### **1. Augmentation de l'Engagement**
- ✅ Plus d'utilisateurs peuvent jouer
- ✅ Plus de pronostics = plus de données
- ✅ Meilleure rétention

### **2. Incitation aux Visites PDV**
- ✅ +4 points/jour = forte incitation
- ✅ Les PDV deviennent un avantage compétitif
- ✅ Trackable via `PointsLog` (source: `venue_visit`)

### **3. Performance Améliorée**
- ✅ Back button ultra-rapide (bfcache)
- ✅ Moins de requêtes serveur (cache 5 min)
- ✅ Meilleure expérience mobile

---

## 🔍 Surveillance & Metrics

### **Metrics à Suivre:**

```sql
-- Pronostics avec vs sans venue
SELECT 
    COUNT(*) as total_predictions,
    COUNT(CASE WHEN bar_id IS NOT NULL THEN 1 END) as with_venue,
    COUNT(CASE WHEN bar_id IS NULL THEN 1 END) as without_venue
FROM predictions;

-- Points bonus venue distribués
SELECT 
    DATE(created_at) as date,
    COUNT(*) as bonus_awarded,
    SUM(points) as total_bonus_points
FROM point_logs
WHERE source = 'venue_visit'
GROUP BY DATE(created_at)
ORDER BY date DESC;

-- Top PDVs (check-ins)
SELECT 
    b.name,
    b.zone,
    COUNT(*) as checkins,
    SUM(pl.points) as total_bonus_distributed
FROM bars b
JOIN point_logs pl ON b.id = pl.bar_id
WHERE pl.source = 'venue_visit'
GROUP BY b.id
ORDER BY checkins DESC
LIMIT 10;
```

---

## ⚙️ Configuration Avancée

### **Mode "Événement Spécial"**

Pour forcer le geofencing lors d'un événement:

```env
# Dans .env production
REQUIRE_VENUE_GEOFENCING=true
VENUE_BONUS_POINTS=10          # Doubler les points
VENUE_PROXIMITY_RADIUS_KM=0.5  # Rayon élargi
```

### **Mode "Test"**

Pour tester sans contraintes:

```env
REQUIRE_VENUE_GEOFENCING=false
VENUE_BONUS_POINTS=0  # Désactiver bonus temporairement
```

---

## 🐛 Troubleshooting

### **1. "Je ne reçois pas les points bonus venue"**

**Vérifications:**
```php
// 1. Vérifier que le venue est actif
Bar::where('id', $venueId)->where('is_active', true)->exists();

// 2. Vérifier que c'est la première fois aujourd'hui
PointLog::where('user_id', $userId)
    ->where('source', 'venue_visit')
    ->whereDate('created_at', Carbon::today())
    ->exists(); // Doit être false

// 3. Vérifier la distance
$distance = GeolocationService::calculateHaversineDistance(...);
// Doit être <= 0.2 km (200m)
```

### **2. "Les utilisateurs peuvent toujours pronostiquer partout"**

**Vérification:**
```bash
# Vérifier .env
grep REQUIRE_VENUE_GEOFENCING .env
# Doit afficher: REQUIRE_VENUE_GEOFENCING=false

# Si vous voulez forcer le PDV
php artisan config:clear
# Puis changer dans .env: REQUIRE_VENUE_GEOFENCING=true
```

### **3. "Le back button est lent"**

**Vérifications:**
```javascript
// Ouvrir Console navigateur (F12)
// Chercher: "[GAZELLE] Page restored from bfcache"

// Si absent, le bfcache n'est pas actif (normal sur dev parfois)
// En production, devrait fonctionner automatiquement
```

---

## 📞 Support

**Questions ou bugs:**
- Email: jeanluc@bigfiveabidjan.com
- Documentation: Ce fichier + `HERO_AND_MATCHES_UPDATE.md` + `HOT_RELOAD_GUIDE.md`

---

**Dernière mise à jour:** Décembre 2024  
**Développé par:** Big Five Abidjan  
**Projet:** GAZELLE - Le goût de notre victoire
