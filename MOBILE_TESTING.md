# 📱 Guide de Test Mobile avec Hot Reload

## ✅ Configuration Implémentée

BrowserSync-like functionality a été configuré via Vite pour permettre le test en temps réel sur mobile !

### Modifications effectuées:

1. ✅ **vite.config.js** - Configuration HMR avec IP dynamique
2. ✅ **package.json** - Nouveau script `mobile` pour tests mobile
3. ✅ **scripts/show-mobile-url.js** - Affichage automatique de l'URL mobile

---

## 🚀 Comment Tester sur Mobile

### Étape 1: Vérifier Docker

```bash
# Assurez-vous que Docker est lancé
docker compose up -d
```

### Étape 2: Lancer Vite en Mode Mobile

```bash
# Avec npm
npm run mobile

# OU avec yarn
yarn mobile
```

Cette commande va:
- Afficher votre adresse IP locale
- Afficher l'URL à utiliser sur votre mobile
- Démarrer Vite avec HMR activé

### Étape 3: Accéder depuis Votre Mobile

**Sur votre téléphone:**
1. Connectez-vous au **MÊME réseau WiFi** que votre ordinateur
2. Ouvrez le navigateur (Safari, Chrome, etc.)
3. Tapez l'URL affichée dans le terminal (ex: `http://192.168.1.100`)

---

## 🎯 Fonctionnalités

### Hot Module Replacement (HMR)

Les changements se reflètent **instantanément** sur mobile lorsque vous modifiez:

- ✅ Fichiers Blade (`.blade.php`)
- ✅ CSS (`resources/css/app.css`)
- ✅ JavaScript (`resources/js/app.js`)
- ✅ Contrôleurs PHP
- ✅ Routes

### Rechargement Automatique

Vite détecte automatiquement les changements et rafraîchit:
- Les styles CSS (sans recharger la page)
- Le JavaScript (avec rechargement partiel)
- Les vues Blade (rechargement complet de la page)

---

## 🔧 Configuration Technique

### vite.config.js - Points Clés

```javascript
// Détection automatique de l'IP locale
function getLocalIP() {
    const interfaces = os.networkInterfaces();
    for (const name of Object.keys(interfaces)) {
        for (const iface of interfaces[name]) {
            if (iface.family === 'IPv4' && !iface.internal) {
                return iface.address;
            }
        }
    }
    return 'localhost';
}

// HMR configuré avec IP dynamique
server: {
    host: '0.0.0.0',     // Écoute sur toutes les interfaces
    port: 5173,
    hmr: {
        host: host,       // IP locale détectée dynamiquement
        protocol: 'ws',
        port: 5173,
    },
}
```

### Fichiers Surveillés

```javascript
refresh: [
    'resources/views/**/*.blade.php',
    'resources/views/**/**/*.blade.php',
    'app/Http/Controllers/**/*.php',
    'routes/**/*.php',
]
```

---

## 🐛 Dépannage

### Problème 1: "Impossible de se connecter" sur mobile

**Causes possibles:**
1. Mobile et ordinateur sur des réseaux WiFi différents
2. Firewall bloque le port 5173
3. Docker n'est pas lancé

**Solutions:**
```bash
# 1. Vérifier que Docker tourne
docker ps

# 2. Autoriser le port 5173 dans le firewall
# Sur macOS:
sudo /usr/libexec/ApplicationFirewall/socketfilterfw --add /usr/local/bin/node
sudo /usr/libexec/ApplicationFirewall/socketfilterfw --unblockapp /usr/local/bin/node

# 3. Redémarrer Vite
npm run mobile
```

### Problème 2: Les changements ne se reflètent pas

**Solution:**
```bash
# Nettoyer les caches
php artisan view:clear
php artisan config:clear
php artisan cache:clear

# Redémarrer Vite
npm run mobile
```

### Problème 3: Erreur "IP non détectée"

**Solution:**
Utilisez manuellement votre IP locale:

```bash
# Trouver votre IP
# Sur macOS:
ifconfig | grep "inet " | grep -v 127.0.0.1

# Sur Linux:
ip addr show | grep "inet " | grep -v 127.0.0.1

# Puis accédez manuellement:
# http://VOTRE_IP (sur mobile)
```

### Problème 4: Hot Reload ne fonctionne pas

**Vérifier que Vite est bien connecté:**
1. Ouvrez la console du navigateur sur mobile
2. Cherchez `[vite] connected` dans les logs
3. Si absent, vérifiez l'URL HMR dans la console

---

## 📋 Commandes Utiles

### Scripts NPM/Yarn

```bash
# Développement normal (localhost uniquement)
npm run dev

# Développement avec accès réseau (recommandé)
npm run hot

# Test mobile avec affichage des URLs
npm run mobile

# Build de production
npm run build

# Watch mode (rebuild automatique)
npm run watch
```

### Docker

```bash
# Démarrer les services
docker compose up -d

# Voir les logs Laravel
docker compose logs -f laravel.test

# Entrer dans le conteneur
docker exec -it landingpagecan-laravel.test-1 bash

# Arrêter les services
docker compose down
```

---

## 🎨 Tester les Fonctionnalités Responsive

### Checklist de Test Mobile

Une fois connecté depuis votre mobile, testez:

#### 1. Page `/matches`
- ✅ Grille 2x2 sur desktop devient 1 colonne sur mobile
- ✅ Onglets des phases (responsive améliioré - à venir)
- ✅ Chips PDV colorés lisibles
- ✅ Drapeaux s'affichent correctement
- ✅ Stade affiché sous chaque match

#### 2. Page `/map`
- ✅ Carte Leaflet interactive
- ✅ Icônes différentes par type PDV (bleu/vert/orange/violet)
- ✅ Légende visible en bas
- ✅ Popup des marqueurs

#### 3. Bannière Géolocalisation
- ✅ S'affiche quand à moins de 500m d'un PDV
- ✅ Message spécial quand à moins de 50m
- ✅ Fermeture automatique après 15s

#### 4. Menu Navigation
- ✅ Menu hamburger à 1024px (à implémenter)
- ✅ Navigation fluide entre pages

---

## 💡 Astuces de Développement

### 1. Tester Plusieurs Appareils Simultanément

Vous pouvez ouvrir l'URL sur plusieurs appareils en même temps:
- Votre mobile
- Une tablette
- Un autre ordinateur

Tous verront les changements en temps réel !

### 2. Inspecter sur Mobile

**iOS (Safari):**
1. Activez "Développement Web" dans Réglages > Safari > Avancé
2. Connectez l'iPhone à votre Mac via USB
3. Ouvrez Safari > Développement > [Votre iPhone]

**Android (Chrome):**
1. Activez "Options pour développeurs" sur Android
2. Activez "Débogage USB"
3. Connectez via USB
4. Ouvrez Chrome > chrome://inspect

### 3. Simuler Géolocalisation

Pour tester la bannière de géolocalisation:
1. Utilisez les outils de développement
2. Ouvrez l'onglet "Sensors" ou "Location"
3. Saisissez les coordonnées GPS d'un PDV

### 4. Tester le Touch vs Click

Certains événements JavaScript se comportent différemment:
- Desktop: `click`, `hover`
- Mobile: `touchstart`, `touchend`

Alpine.js gère automatiquement ces différences.

---

## 📊 Comparaison avec BrowserSync

### Similitudes:
✅ Rechargement automatique multi-appareils
✅ Synchronisation en temps réel
✅ HMR (Hot Module Replacement)
✅ Accessible via IP locale

### Avantages de Vite:
✅ Plus rapide que BrowserSync
✅ Natif avec Laravel
✅ Pas de proxy supplémentaire
✅ Meilleure gestion des assets
✅ Support TypeScript/JSX natif

### BrowserSync aurait apporté:
- Synchronisation des clics entre appareils
- Synchronisation du scroll
- Formulaires synchronisés

**Verdict:** Vite suffit amplement pour tester le responsive et les fonctionnalités !

---

## 🔐 Sécurité

### Ports Exposés

- **5173** - Vite Dev Server (HMR)
- **80** - Application Laravel via Docker

### Recommandations

1. ✅ Utilisez uniquement sur votre réseau local privé
2. ✅ Ne partagez pas votre IP publique
3. ✅ Le mode dev n'est pas pour la production
4. ✅ Désactivez Vite après vos tests

---

## 📝 Exemple de Session de Test

```bash
# 1. Démarrer Docker
$ docker compose up -d
Starting landingpagecan-mysql-1 ... done
Starting landingpagecan-laravel.test-1 ... done

# 2. Lancer Vite en mode mobile
$ npm run mobile

============================================================
📱 ACCÈS MOBILE - URLs de test
============================================================

🌐 Adresse IP locale: 192.168.1.42

📲 Accédez à votre application depuis votre mobile:

   → Application: http://192.168.1.42
   → Vite HMR:    http://192.168.1.42:5173

💡 Assurez-vous que:
   1. Votre mobile est sur le MÊME réseau WiFi
   2. Docker est lancé (docker compose up -d)
   3. Vite tourne (npm run mobile ou yarn mobile)
   4. Le firewall autorise les connexions sur le port 5173

============================================================

  VITE v5.x.x  ready in 523 ms

  ➜  Local:   http://localhost:5173/
  ➜  Network: http://192.168.1.42:5173/
  ➜  press h to show help

# 3. Sur votre mobile, ouvrez: http://192.168.1.42

# 4. Modifiez un fichier (ex: resources/views/matches.blade.php)
#    → La page se recharge automatiquement sur mobile !

# 5. Modifier un fichier CSS (ex: resources/css/app.css)
#    → Les styles se mettent à jour sans recharger la page !
```

---

## 🎯 Prochaines Étapes

Avec le mobile testing configuré, vous pouvez maintenant:

1. ✅ Tester le responsive des onglets sur mobile
2. ✅ Vérifier le menu hamburger (après implémentation)
3. ✅ Tester la géolocalisation sur appareil réel
4. ✅ Valider la grille 2x2 des matchs
5. ✅ Vérifier l'affichage des drapeaux
6. ✅ Tester les chips PDV sur petit écran

---

**Date:** 19 Décembre 2025
**Status:** ✅ Mobile Testing Configuré
**Commande:** `npm run mobile` ou `yarn mobile`
