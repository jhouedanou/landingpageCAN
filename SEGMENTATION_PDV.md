# Segmentation des Points de Vente - GAZELLE

## 📋 Vue d'Ensemble

Ce document décrit le système complet de segmentation des PDV (Points De Vente) partenaires permettant à l'administrateur de catégoriser et gérer les lieux sans intervention technique.

---

## 🎯 **Objectifs**

✅ **Segmenter les PDV en 4 catégories:**
1. **Dakar** 🏙️ - Points de vente dans la capitale
2. **Régions** 🗺️ - Points de vente hors Dakar
3. **CHR** 🍽️ - Cafés-Hôtel-Restaurants
4. **Fanzones** 🎉 - Zones de fans et événements

✅ **Interface admin autonome** - Le client peut gérer lui-même la segmentation

✅ **Actions groupées** - Modifier plusieurs PDV en un seul clic

✅ **Filtrage avancé** - Rechercher et filtrer par catégorie, zone, statut

---

## 🗄️ **Base de Données**

### **Migration:**
`database/migrations/2025_12_19_153500_add_type_pdv_to_bars_table.php`

**Champs ajoutés à `bars`:**

```php
// Type de PDV (enum)
type_pdv ENUM('dakar', 'regions', 'chr', 'fanzone') DEFAULT 'dakar'

// Zone géographique (optionnel)
zone VARCHAR(100) NULL

// Index pour performance
INDEX(type_pdv)
INDEX(zone)
```

**Exécution:**
```bash
php artisan migrate
```

---

## 📊 **Modèle Bar**

### **Champs Fillable:**
```php
protected $fillable = [
    'name',
    'address',
    'zone',
    'latitude',
    'longitude',
    'is_active',
    'type_pdv',  // ← Nouveau
];
```

### **Méthodes Helper:**

```php
// Obtenir les options de type PDV
Bar::getTypePdvOptions()
// Returns:
[
    'dakar' => 'Points de vente Dakar',
    'regions' => 'Points de vente Régions',
    'chr' => 'Cafés-Hôtel-Restaurants (CHR)',
    'fanzone' => 'Fanzones',
]

// Obtenir le nom lisible
$bar->type_pdv_name
// Returns: "Points de vente Dakar"
```

---

## 🎨 **Interface Admin**

### **1. Page Liste PDV**

**Route:** `/admin/venues`

**Fonctionnalités:**

#### **A. Statistiques:**
```
┌─────────────────┬─────────────┬─────────────┬──────────────┐
│ Total PDV: 45   │ Dakar: 25   │ Régions: 15 │ CHR + FZ: 5  │
│ Actifs: 40      │             │             │              │
│ Inactifs: 5     │             │             │              │
└─────────────────┴─────────────┴─────────────┴──────────────┘
```

#### **B. Filtres:**
- 🔍 **Recherche** par nom
- 📍 **Type PDV** (dropdown)
- 🗺️ **Zone** (texte libre)
- ✅ **Statut** (actif/inactif)

#### **C. Tableau:**
```
┌────┬──────────┬─────────────┬──────────┬──────────┬────────┬─────────┐
│ ☑  │ Nom      │ Type PDV    │ Zone     │ Adresse  │ Statut │ Actions │
├────┼──────────┼─────────────┼──────────┼──────────┼────────┼─────────┤
│ ☑  │ Le Djolof│ 🏙️ Dakar   │ Plateau  │ Rue 5... │ Actif  │ Modifier│
│ ☐  │ Chez Ali │ 🍽️ CHR     │ Almadies │ Corniche │ Actif  │ Supprimer│
└────┴──────────┴─────────────┴──────────┴──────────┴────────┴─────────┘
```

#### **D. Actions Groupées:**

**Panneau affiché quand des PDV sont sélectionnés:**
```
┌──────────────────────────────────────────────────────────┐
│ 3 PDV sélectionné(s)                                     │
│                                                           │
│ [Type PDV ▼] [Appliquer Type]  [Zone____] [Appliquer Zone]│
└──────────────────────────────────────────────────────────┘
```

**Exemple d'utilisation:**
1. Cocher 5 PDV
2. Sélectionner "Régions" dans le dropdown
3. Cliquer "Appliquer Type"
4. → Les 5 PDV passent en catégorie "Régions"

---

### **2. Page Modification PDV**

**Route:** `/admin/venues/{id}/edit`

**Formulaire:**

```
Nom du PDV *: [________________]

Type de PDV *: [Sélectionner ▼]
               - Points de vente Dakar
               - Points de vente Régions
               - Cafés-Hôtel-Restaurants (CHR)
               - Fanzones

Zone: [________________]
      (Ex: Plateau, Almadies, Thiès...)

Adresse *: [________________]
           [________________]

Latitude *: [14.6937000]
Longitude *: [-17.4441000]

☑ PDV Actif
  Si décoché, le PDV ne sera pas visible dans l'application

[Enregistrer les modifications] [Annuler]
```

---

## 🔧 **Routes API**

### **Liste des Routes:**

```php
// Resource CRUD
GET    /admin/venues              → index()   Liste avec filtres
GET    /admin/venues/create       → create()  Formulaire création
POST   /admin/venues              → store()   Créer PDV
GET    /admin/venues/{id}/edit    → edit()    Formulaire modification
PUT    /admin/venues/{id}         → update()  Modifier PDV
DELETE /admin/venues/{id}         → destroy() Supprimer PDV

// Actions groupées
POST   /admin/venues/bulk-update-type  → bulkUpdateType()
POST   /admin/venues/bulk-update-zone  → bulkUpdateZone()
```

---

## 💻 **Controller**

### **VenueController:**

**Méthodes principales:**

```php
// Liste avec filtres
index(Request $request)
- Filtrage par: type_pdv, zone, is_active, search
- Statistiques par type
- Pagination: 20 items/page

// Actions groupées
bulkUpdateType(Request $request)
- Validation: venue_ids[], type_pdv
- Update multiple en une requête

bulkUpdateZone(Request $request)
- Validation: venue_ids[], zone
- Réassignation de zone massive
```

---

## 📱 **Utilisation**

### **Scénario 1: Catégoriser un nouveau PDV**

```
1. Admin va sur /admin/venues
2. Clic "Nouveau PDV"
3. Remplit le formulaire:
   - Nom: "Chez Modou"
   - Type: "Régions"
   - Zone: "Thiès"
   - Adresse, coordonnées...
4. Clic "Créer"
5. → PDV créé et catégorisé automatiquement
```

### **Scénario 2: Recatégoriser plusieurs PDV**

```
1. Admin filtre: Type = "Dakar"
2. Coche 10 PDV de banlieue
3. Dans actions groupées:
   - Sélectionne "Régions"
   - Clic "Appliquer Type"
4. → Les 10 PDV passent en "Régions"
```

### **Scénario 3: Réorganiser par zone**

```
1. Admin filtre: Type = "Régions"
2. Coche tous les PDV de Thiès
3. Dans actions groupées:
   - Entre "Thiès Centre"
   - Clic "Appliquer Zone"
4. → Tous reçoivent zone "Thiès Centre"
```

---

## 🎨 **Design & UX**

### **Badges Type PDV:**

```css
/* Dakar */
🏙️ bg-blue-100 text-blue-800

/* Régions */
🗺️ bg-green-100 text-green-800

/* CHR */
🍽️ bg-orange-100 text-orange-800

/* Fanzones */
🎉 bg-purple-100 text-purple-800
```

### **Statistiques:**

Cartes colorées avec icônes:
- Total: Bleu
- Dakar: Bleu
- Régions: Vert
- CHR + Fanzones: Orange

---

## 🔍 **Filtrage Avancé**

### **Combinaisons possibles:**

```
Recherche: "Chez"
Type: CHR
Zone: Plateau
Statut: Actif
→ Résultat: Tous les CHR actifs du Plateau dont le nom contient "Chez"
```

### **Performance:**

Index sur `type_pdv` et `zone` pour requêtes rapides:
```sql
SELECT * FROM bars 
WHERE type_pdv = 'dakar' 
  AND zone LIKE '%Plateau%' 
  AND is_active = 1
-- ← Utilise les index, très rapide
```

---

## 📊 **Statistiques**

### **Dashboard Admin:**

```
Total PDV: 45
├─ Dakar: 25 (56%)
├─ Régions: 15 (33%)
├─ CHR: 3 (7%)
└─ Fanzones: 2 (4%)

Par statut:
├─ Actifs: 40 (89%)
└─ Inactifs: 5 (11%)
```

---

## 🚀 **Cas d'Usage**

### **1. Organisation Événement:**

**Problème:** Organiser viewing party dans plusieurs villes

**Solution:**
```
1. Filtrer Type = "Fanzones"
2. Voir tous les lieux adaptés
3. Bulk update zone → "CHAN 2026"
4. → Tous les fanzones regroupés pour l'événement
```

### **2. Campagne Marketing:**

**Problème:** Cibler les CHR pour promotion spéciale

**Solution:**
```
1. Filtrer Type = "CHR"
2. Exporter la liste
3. → Campagne SMS/Email ciblée uniquement CHR
```

### **3. Analyse Géographique:**

**Problème:** Savoir combien de PDV hors Dakar

**Solution:**
```
1. Dashboard → Voir statistiques
2. Régions: 15 PDV
3. Filtrer Type = "Régions"
4. → Liste détaillée par zone
```

---

## 🔐 **Permissions**

**Accès:** Réservé aux admins avec middleware `check.admin`

**Actions autorisées:**
- ✅ Voir tous les PDV
- ✅ Créer/Modifier/Supprimer PDV
- ✅ Changer catégorie PDV
- ✅ Réassigner zone
- ✅ Actions groupées

---

## 📈 **Extensions Futures**

### **Court terme:**
- [ ] Export CSV par catégorie
- [ ] Importation bulk avec type_pdv
- [ ] Graphiques statistiques

### **Moyen terme:**
- [ ] Sous-catégories (Bar, Restaurant, Hôtel dans CHR)
- [ ] Tags personnalisés
- [ ] Historique des changements de catégorie

### **Long terme:**
- [ ] Géofencing automatique par zone
- [ ] Propositions IA de catégorie basée sur nom/adresse
- [ ] Clustering géographique intelligent

---

## 🐛 **Troubleshooting**

### **Problème: Les filtres ne fonctionnent pas**

**Cause:** URL mal formée

**Solution:**
```php
// Vérifier la route
Route::get('/admin/venues', [VenueController::class, 'index']);

// Vérifier les paramètres
?type_pdv=dakar&zone=Plateau
```

### **Problème: Actions groupées ne s'affichent pas**

**Cause:** JavaScript non chargé

**Solution:**
```javascript
// Vérifier dans la console
updateBulkPanel(); // Devrait fonctionner
```

### **Problème: Type PDV non mis à jour**

**Cause:** Validation échouée

**Solution:**
```php
// Vérifier les valeurs valides
'type_pdv' => 'required|in:dakar,regions,chr,fanzone'
```

---

## ✅ **Checklist Déploiement**

- [ ] Exécuter migration: `php artisan migrate`
- [ ] Vérifier routes admin accessibles
- [ ] Tester création PDV avec chaque type
- [ ] Tester modification catégorie
- [ ] Tester actions groupées (type + zone)
- [ ] Tester filtres
- [ ] Vérifier statistiques correctes
- [ ] Tester sur mobile (responsive)

---

## 📚 **Références**

**Fichiers créés:**
- `database/migrations/2025_12_19_153500_add_type_pdv_to_bars_table.php`
- `app/Models/Bar.php` (modifié)
- `app/Http/Controllers/Admin/VenueController.php`
- `resources/views/admin/venues/index.blade.php`
- `resources/views/admin/venues/edit.blade.php`
- `routes/web.php` (routes ajoutées)

**Routes:**
```
/admin/venues              → Liste PDV
/admin/venues/create       → Nouveau PDV
/admin/venues/{id}/edit    → Modifier PDV
```

---

**Dernière mise à jour:** 19 Décembre 2024  
**Développé par:** Big Five Abidjan  
**Projet:** GAZELLE - Segmentation PDV autonome 🎯
