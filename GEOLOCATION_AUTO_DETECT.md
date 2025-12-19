# Détection Automatique de Géolocalisation - GAZELLE

## 🎯 Fonctionnalité

Détection automatique de la position de l'utilisateur et affichage d'une **bannière discrète en bas de page** si un PDV partenaire est à proximité (≤ 5 km).

---

## ✨ Caractéristiques

### **1. Détection Automatique**
- ✅ S'active **automatiquement** 2 secondes après le chargement de la page
- ✅ **Pas de popup intrusif** - demande de permission navigateur uniquement
- ✅ Une seule vérification par session (stockée dans `sessionStorage`)
- ✅ Cache de 5 minutes pour la position GPS

### **2. Bannière Discrète**
```
┌─────────────────────────────────────────────────┐
│ 📍 Le Djolof à 1.2 km                          │
│ 🎉 Gagnez +4 pts bonus en pronostiquant ici!  │
│                        [Voir carte] [✕]        │
└─────────────────────────────────────────────────┘
```

**Position:** Bas de page (fixed bottom)  
**Style:** Gradient bleu avec bordure orange  
**Animation:** Slide up depuis le bas  
**Durée:** Auto-fermeture après 15 secondes

### **3. Conditions d'Affichage**
```javascript
✅ Utilisateur connecté
✅ Géolocalisation disponible et autorisée
✅ PDV partenaire dans un rayon de 5 km
✅ Première visite de la session
```

---

## 🔧 Implémentation

### **Fichiers Créés**

#### **1. Composant Blade**
`resources/views/components/geolocation-banner.blade.php`

**Props:**
```php
@props(['venues']) // Collection de PDV actifs
```

**Fonctionnalités:**
- Détection GPS automatique
- Calcul distance vers tous les PDV
- Sélection du PDV le plus proche
- Animation smooth de la bannière

#### **2. Intégration Layout**
`resources/views/components/layouts/app.blade.php`

**Position:** Entre `<main>` et `<footer>`

**Condition:**
```blade
@if(session('user_id'))
    <x-geolocation-banner :venues="$activeVenues" />
@endif
```

---

## 📊 Logique de Fonctionnement

### **Étape 1: Init (2s après chargement)**
```javascript
x-init="setTimeout(() => checkGeolocation(), 2000)"
```

### **Étape 2: Vérification Session**
```javascript
hasChecked: sessionStorage.getItem('geo_checked') === 'true'
```
- Si déjà vérifié → **Stop**
- Sinon → Continue

### **Étape 3: Demande Position**
```javascript
navigator.geolocation.getCurrentPosition(
    resolve,
    reject,
    { 
        enableHighAccuracy: false,  // Économise batterie
        timeout: 5000,               // 5 secondes max
        maximumAge: 300000          // Cache 5 minutes
    }
);
```

### **Étape 4: Calcul Distance**
```javascript
// Formule Haversine
distance = R * c  // R = 6371 km (rayon Terre)
```

### **Étape 5: Filtrage**
```javascript
if (closestVenue && minDistance <= 5) {
    // Afficher bannière
}
```

### **Étape 6: Affichage**
```javascript
show = true;
setTimeout(() => show = false, 15000);  // Auto-fermeture 15s
```

---

## 🎨 Design de la Bannière

### **Structure HTML**
```html
<div class="fixed bottom-0 left-0 right-0 z-50">
    <div class="bg-gradient-to-r from-soboa-blue to-blue-600">
        <!-- Icône + Message + Actions -->
        <div class="flex items-center justify-between">
            <!-- Gauche: Icône localisation + Info PDV -->
            <!-- Droite: Bouton "Voir carte" + Bouton fermer -->
        </div>
        
        <!-- Barre de progression auto-fermeture -->
        <div class="h-1 bg-white/20">
            <div class="bg-soboa-orange" style="width: 0%"></div>
        </div>
    </div>
</div>
```

### **Couleurs**
- **Fond:** Gradient `from-soboa-blue to-blue-600` (#003399)
- **Bordure top:** `border-soboa-orange` (#FFD700)
- **Texte:** Blanc
- **Bouton CTA:** Orange avec texte noir

### **Responsive**
```css
Mobile:  Texte xs/sm, bouton compact
Desktop: Texte sm/base, bouton normal
```

---

## 🚀 Actions Disponibles

### **1. Voir sur la carte**
```javascript
goToVenue() {
    window.location.href = '/map';
}
```
Redirige vers `/map` avec tous les PDV affichés

### **2. Fermer**
```javascript
closeBanner() {
    this.show = false;
}
```
Cache la bannière immédiatement

### **3. Auto-fermeture**
```javascript
setTimeout(() => {
    this.show = false;
}, 15000);  // 15 secondes
```
La bannière disparaît automatiquement

---

## 🔐 Sécurité & Performance

### **Performance**
✅ **Cache GPS:** 5 minutes (pas de requête constante)  
✅ **Session Storage:** Une seule vérification par session  
✅ **Timeout:** 5 secondes max pour GPS  
✅ **Lazy Load:** Chargement différé de 2 secondes  

### **Privacy**
✅ **Permission requise:** Le navigateur demande autorisation  
✅ **Opt-in:** L'utilisateur peut refuser  
✅ **Pas de tracking:** Position non stockée côté serveur  
✅ **Session only:** Données perdues à la fermeture du navigateur  

### **Fallback**
```javascript
try {
    await getCurrentPosition();
} catch (error) {
    console.log('Geolocation denied');
    sessionStorage.setItem('geo_checked', 'true');
}
```
Si refusé → marque comme vérifié (pas de re-demande)

---

## 📱 Mobile Friendly

### **Safe Area**
```css
.safe-bottom {
    padding-bottom: env(safe-area-inset-bottom);
}
```
Respect des encoches iPhone/Android

### **Touch Optimized**
- Boutons suffisamment grands (44px min)
- Espacement généreux
- Pas de hover states sur mobile

---

## 🧪 Test en Dev

### **1. Simuler Position**
```javascript
// Dans DevTools Console
navigator.geolocation.getCurrentPosition = (success) => {
    success({
        coords: {
            latitude: 14.7517342,   // Dakar
            longitude: -17.381228
        }
    });
};
```

### **2. Reset Session**
```javascript
sessionStorage.removeItem('geo_checked');
location.reload();
```

### **3. Vérifier Distance**
```javascript
// Dans Alpine DevTools
$data.nearbyVenue  // PDV trouvé
$data.distance     // Distance en km
```

---

## 🎛️ Configuration

### **Rayon de Détection**
```javascript
// Dans geolocation-banner.blade.php
if (closestVenue && minDistance <= 5) {  // 5 km par défaut
```

**Modifier:**
```javascript
minDistance <= 10  // 10 km
minDistance <= 2   // 2 km (plus strict)
```

### **Durée Affichage**
```javascript
setTimeout(() => {
    this.show = false;
}, 15000);  // 15 secondes
```

**Modifier:**
```javascript
}, 10000);  // 10 secondes
}, 30000);  // 30 secondes
```

### **Délai Initial**
```javascript
x-init="setTimeout(() => checkGeolocation(), 2000)"
```

**Modifier:**
```javascript
}, 1000)"   // 1 seconde
}, 5000)"   // 5 secondes
```

---

## 🎯 Scénarios d'Usage

### **Scénario 1: Utilisateur proche PDV**
```
1. User visite /matches
2. Après 2s → Détection GPS
3. PDV "Le Djolof" trouvé à 1.2 km
4. Bannière s'affiche en bas
5. Message: "📍 Le Djolof à 1.2 km - +4 pts bonus!"
6. User clique "Voir carte" → Redirigé vers /map
```

### **Scénario 2: Utilisateur loin de tout PDV**
```
1. User visite /dashboard
2. Après 2s → Détection GPS
3. PDV le plus proche à 12 km
4. Bannière NE s'affiche PAS (> 5 km)
5. Session marquée comme vérifiée
```

### **Scénario 3: Permission refusée**
```
1. User visite /
2. Après 2s → Demande permission GPS
3. User refuse
4. Bannière NE s'affiche PAS
5. Session marquée (pas de re-demande)
```

### **Scénario 4: Déjà vérifié**
```
1. User visite /matches (déjà vérifié)
2. sessionStorage.geo_checked = 'true'
3. Aucune détection GPS
4. Bannière NE s'affiche PAS
```

---

## 📊 Analytics

### **Tracking (optionnel)**

**Ajouter dans le composant:**
```javascript
// Quand bannière affichée
gtag('event', 'geolocation_banner_shown', {
    venue_name: this.nearbyVenue.name,
    distance: this.distance
});

// Quand user clique "Voir carte"
gtag('event', 'geolocation_cta_click', {
    venue_name: this.nearbyVenue.name
});
```

---

## ✅ Checklist

- [x] Composant créé (`geolocation-banner.blade.php`)
- [x] Intégration layout (`app.blade.php`)
- [x] Détection automatique (2s delay)
- [x] Cache session storage
- [x] Bannière discrète en bas
- [x] Auto-fermeture 15s
- [x] Bouton "Voir carte"
- [x] Bouton fermer
- [x] Responsive mobile/desktop
- [x] Safe area iOS/Android
- [x] Animations smooth
- [x] Performance optimisée
- [x] Privacy respectée

---

## 🚀 Déploiement

**Aucune migration nécessaire!**

La fonctionnalité est **100% frontend** et s'active automatiquement pour tous les utilisateurs connectés.

**Activation:**
```bash
# Aucune commande requise
# La bannière s'active automatiquement
```

---

**Créé:** 19 Décembre 2024  
**Auteur:** Big Five Abidjan  
**Projet:** GAZELLE - Auto Geolocation Banner  
**Version:** 1.0
