# Guide Hot Reload - GAZELLE

## 🔥 Hot Module Replacement (HMR) avec Vite

Le projet GAZELLE est maintenant configuré avec un **hot reload automatique** qui rafraîchit votre navigateur instantanément lorsque vous modifiez le code.

---

## 🚀 Démarrage Rapide

### **Méthode 1: Script Automatique (Recommandé)**

```bash
./dev.sh
```

Ce script lance automatiquement:
- ✅ Laravel (`php artisan serve`) sur http://localhost:8000
- ✅ Vite (Hot Reload) sur http://localhost:5173
- ✅ Affiche des messages clairs dans le terminal

**Pour arrêter:** Appuyez sur `Ctrl+C`

---

### **Méthode 2: Commandes Manuelles**

Dans **2 terminaux séparés**:

**Terminal 1 - Laravel:**
```bash
php artisan serve
```

**Terminal 2 - Vite Hot Reload:**
```bash
npm run dev
```

---

## 📋 Scripts NPM Disponibles

| Commande | Description | Utilisation |
|----------|-------------|-------------|
| `npm run dev` | Hot reload local | Développement normal |
| `npm run hot` | Hot reload réseau | Accessible depuis mobile/tablette |
| `npm run build` | Build production | Avant déploiement |
| `npm run watch` | Build auto | Alternative au hot reload |
| `npm run preview` | Preview build | Tester le build localement |

---

## 🔥 Fichiers Surveillés (Auto-Refresh)

Le hot reload est activé pour:

### **1. Frontend (HMR Instantané)**
- ✅ **CSS:** `resources/css/**/*.css`
- ✅ **JavaScript:** `resources/js/**/*.js`

### **2. Backend (Rafraîchissement Complet)**
- ✅ **Vues Blade:** `resources/views/**/*.blade.php`
- ✅ **Controllers:** `app/Http/Controllers/**/*.php`
- ✅ **Routes:** `routes/**/*.php`

---

## 📱 Tester sur Mobile/Tablette

Pour accéder au hot reload depuis un appareil mobile sur le même réseau:

### **1. Démarrer avec `--host`**
```bash
npm run hot
```

### **2. Trouver votre IP locale**
```bash
# Mac/Linux
ifconfig | grep "inet " | grep -v 127.0.0.1

# Windows
ipconfig
```

### **3. Accéder depuis mobile**
```
http://VOTRE_IP:8000
```

**Exemple:** `http://192.168.1.100:8000`

---

## ⚙️ Configuration Vite

### **`vite.config.js`**

```javascript
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: [
                'resources/views/**/*.blade.php',
                'app/Http/Controllers/**/*.php',
                'routes/**/*.php',
            ],
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
            protocol: 'ws',
        },
        watch: {
            usePolling: true,
            interval: 100,
        },
    },
});
```

### **Paramètres Clés:**

| Paramètre | Valeur | Explication |
|-----------|--------|-------------|
| `refresh` | Array de patterns | Fichiers qui déclenchent un refresh complet |
| `usePolling: true` | Booléen | Nécessaire pour Docker/VM |
| `interval: 100` | Millisecondes | Délai de détection (plus rapide = 100ms) |
| `host: '0.0.0.0'` | IP | Accessible depuis réseau local |
| `protocol: 'ws'` | WebSocket | Protocol HMR |

---

## 🐛 Troubleshooting

### **1. Le navigateur ne se rafraîchit pas**

**Vérifier que Vite tourne:**
```bash
# Vous devriez voir "VITE v5.x.x ready in X ms"
npm run dev
```

**Vérifier la connexion HMR:**
- Ouvrir la console du navigateur (F12)
- Rechercher des erreurs WebSocket
- Vous devriez voir: `[vite] connected.`

**Solution:**
```bash
# Arrêter Vite
Ctrl+C

# Clear node_modules et cache
rm -rf node_modules package-lock.json
npm install

# Redémarrer
npm run dev
```

---

### **2. Erreur "Port 5173 already in use"**

**Trouver et tuer le processus:**
```bash
# Mac/Linux
lsof -ti:5173 | xargs kill -9

# Windows
netstat -ano | findstr :5173
taskkill /PID [PID_NUMBER] /F
```

**Ou utiliser un autre port:**
```bash
# Modifier vite.config.js
server: {
    port: 5174,  // Changer le port
}
```

---

### **3. CSS/JS ne se met pas à jour**

**Clear tous les caches:**
```bash
# Laravel
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Navigateur
Ctrl+Shift+R (Hard Reload)

# Vite
rm -rf public/build
npm run dev
```

---

### **4. Modifications Blade ignorées**

**Vérifier que le pattern est correct dans `vite.config.js`:**
```javascript
refresh: [
    'resources/views/**/*.blade.php',  // ✅ Correct
    'resources/views/**/**/*.blade.php', // ✅ Sous-dossiers
]
```

**Si toujours pas, essayer:**
```bash
# Restart complet
Ctrl+C
./dev.sh
```

---

## 💡 Conseils de Performance

### **1. Optimiser le Polling**

Si le hot reload est trop lent:
```javascript
// vite.config.js
watch: {
    usePolling: true,
    interval: 50,  // Plus rapide (par défaut: 100)
}
```

⚠️ **Attention:** Interval trop bas = CPU élevé

---

### **2. Ignorer des Fichiers**

Pour éviter de surveiller des fichiers inutiles:
```javascript
// vite.config.js
watch: {
    ignored: [
        '**/node_modules/**',
        '**/vendor/**',
        '**/storage/**',
        '**/.git/**',
    ],
}
```

---

### **3. Sourcemaps pour Debug**

Les sourcemaps sont activées en dev:
```javascript
// vite.config.js
build: {
    sourcemap: true,  // Debug CSS/JS dans DevTools
}
```

---

## 🎨 Workflow de Développement

### **Flux Typique:**

1. **Démarrer le hot reload:**
   ```bash
   ./dev.sh
   ```

2. **Ouvrir le navigateur:**
   ```
   http://localhost:8000
   ```

3. **Modifier le code:**
   - **CSS:** Changement instantané (HMR)
   - **JS:** Changement instantané (HMR)
   - **Blade:** Refresh complet de la page
   - **PHP:** Refresh complet de la page

4. **Voir les changements:**
   - CSS/JS: **< 1 seconde**
   - Blade/PHP: **1-2 secondes**

---

## 📊 Comparaison: Avant vs Après

### **Avant (Sans Hot Reload):**
1. Modifier CSS
2. `npm run build` (3-5 secondes)
3. Rafraîchir manuellement (F5)
4. Total: **~5-10 secondes**

### **Après (Avec Hot Reload):**
1. Modifier CSS
2. Changement automatique
3. Total: **< 1 seconde** ⚡

---

## 🔒 Production vs Développement

| Environnement | Commande | Build | HMR |
|---------------|----------|-------|-----|
| **Développement** | `npm run dev` | Non | ✅ Oui |
| **Production** | `npm run build` | ✅ Oui | Non |

⚠️ **Important:** Toujours `npm run build` avant de déployer en production!

---

## 🌐 URLs de Développement

### **Application Laravel:**
```
http://localhost:8000
```

### **Vite Dev Server:**
```
http://localhost:5173
```

### **Accès Réseau Local:**
```
http://[VOTRE_IP]:8000
```

---

## 📝 Logs Vite Utiles

### **Connexion réussie:**
```
VITE v5.4.21 ready in 523 ms

➜  Local:   http://localhost:5173/
➜  Network: http://192.168.1.100:5173/
➜  press h to show help
```

### **HMR actif:**
```
[vite] connected.
[vite] hot updated: /resources/css/app.css
```

### **Refresh Blade:**
```
page reload resources/views/welcome.blade.php
```

---

## 🎯 Checklist Rapide

Avant de commencer à coder:

- [ ] Vite tourne (`npm run dev`)
- [ ] Laravel tourne (`php artisan serve`)
- [ ] Navigateur ouvert sur `http://localhost:8000`
- [ ] Console navigateur montre `[vite] connected.`
- [ ] Test: Modifier un CSS → Changement instantané

✅ Tout fonctionne? **Go code!** 🚀

---

## 📞 Support

**Problème avec hot reload?**
1. Vérifier cette documentation
2. Consulter les logs Vite dans le terminal
3. Consulter la console du navigateur (F12)
4. Contact: jeanluc@bigfiveabidjan.com

---

**Dernière mise à jour:** Décembre 2024  
**Développé par:** Big Five Abidjan  
**Projet:** GAZELLE - Le goût de notre victoire
