# 🚀 Guide de Déploiement Production - CAN 2025

## 📋 Vue d'ensemble

Ce guide explique comment déployer l'application en production avec une synchronisation complète des données (équipes, matchs, PDV, animations) **sans affecter les utilisateurs**.

## ✅ Changements Apportés

### 1. Remplacement de "TBD" par "à déterminer"
- ✅ Modèle `MatchGame` : Commentaires mis à jour
- ✅ Vue `map.blade.php` : Variable renommée de `$isTBD` à `$isADeterminer`
- ✅ Composant `match-card.blade.php` : Commentaires en français
- ✅ Seeders : Commentaires traduits en français

### 2. Seeder de Production Définitif
Le seeder `ProductionSeeder.php` est déjà en place et prêt à l'emploi :

**Fonctionnalités :**
- 🔄 Synchronise les données dev → production
- 👤 **Préserve 100% des utilisateurs et leurs prédictions**
- 📄 Import complet depuis `venues.csv`
- ✅ Transactions DB avec rollback automatique
- 📊 Vérifications d'intégrité complètes

**Données importées :**
- ✅ Équipes (avec ISO codes pour les drapeaux)
- ✅ Points de vente (avec coordonnées GPS)
- ✅ Matchs (phases de poule et finales)
- ✅ Animations (liens match-PDV)

**Données préservées :**
- ✅ Utilisateurs (`users`)
- ✅ Prédictions (`predictions`)
- ✅ Logs de points (`point_logs`)

### 3. Script de Déploiement Mis à Jour
Le fichier `deploy.sh` a été modifié pour :
- ✅ Exécuter les migrations
- ✅ Lancer `ProductionSeeder` au lieu du seeder par défaut
- ✅ Optimiser les caches Laravel

## 🎯 Workflow de Déploiement

### Prérequis

1. **Fichier CSV à jour** : `venues.csv` doit être présent à la racine du projet
2. **Tests locaux** : Vérifier que tout fonctionne en local
3. **Backup** : Toujours faire un backup avant déploiement

### Étape 1 : Préparation Locale

```bash
# 1. Tester le seeder localement
php artisan db:seed --class=ProductionSeeder

# 2. Vérifier les données
php artisan tinker --execute="
echo 'Teams: ' . \App\Models\Team::count() . PHP_EOL;
echo 'Venues: ' . \App\Models\Bar::count() . PHP_EOL;
echo 'Matches: ' . \App\Models\MatchGame::count() . PHP_EOL;
echo 'Animations: ' . \App\Models\Animation::count() . PHP_EOL;
echo 'Users: ' . \App\Models\User::count() . PHP_EOL;
"
```

### Étape 2 : Commit et Push

```bash
git add .
git commit -m "feat: Deploy production avec ProductionSeeder

✨ Changements:
- Remplacé 'TBD' par 'à déterminer' dans tout le code
- Script de déploiement utilise ProductionSeeder
- Synchronisation dev → production préservant les users

📊 Seeder ProductionSeeder:
- Import complet depuis venues.csv
- Préserve 100% des utilisateurs et prédictions
- Transactions DB avec rollback automatique
"

git push origin main
```

### Étape 3 : Déploiement Forge

Le script `deploy.sh` s'exécutera automatiquement sur Forge et :

1. ✅ Installera les dépendances Composer (production)
2. ✅ Construira le frontend (npm build)
3. ✅ Exécutera les migrations
4. ✅ **Lancera ProductionSeeder** (synchronisation des données)
5. ✅ Optimisera les caches
6. ✅ Créera les liens de stockage
7. ✅ Redémarrera les queues

### Étape 4 : Vérification Post-Déploiement

```bash
# SSH vers le serveur de production
ssh forge@votresite.com
cd /home/forge/votresite.com

# Vérifier les données
php artisan tinker --execute="
echo 'Teams: ' . \App\Models\Team::count() . PHP_EOL;
echo 'Teams avec ISO: ' . \App\Models\Team::whereNotNull('iso_code')->count() . PHP_EOL;
echo 'Venues: ' . \App\Models\Bar::count() . PHP_EOL;
echo 'Venues avec coords: ' . \App\Models\Bar::whereNotNull('latitude')->count() . PHP_EOL;
echo 'Matches: ' . \App\Models\MatchGame::count() . PHP_EOL;
echo 'Animations: ' . \App\Models\Animation::count() . PHP_EOL;
echo '---' . PHP_EOL;
echo 'Users (PRÉSERVÉS): ' . \App\Models\User::count() . PHP_EOL;
echo 'Predictions (PRÉSERVÉES): ' . \App\Models\Prediction::count() . PHP_EOL;
"

# Vérifier les logs
tail -f storage/logs/laravel.log
```

## 📊 Données Attendues

Après déploiement, vous devriez avoir :

### Données de Planning (SYNCHRONISÉES)
- **Équipes** : ~24 équipes CAN 2025 avec ISO codes
- **PDV** : ~60 points de vente avec coordonnées GPS
- **Matchs** : ~52 matchs (phases de poule + finales)
- **Animations** : ~80+ liens match-PDV

### Données Utilisateurs (PRÉSERVÉES)
- **Users** : Tous les utilisateurs existants
- **Prédictions** : Toutes les prédictions existantes
- **Point Logs** : Tous les logs de points

## 🔧 Utilisation Manuelle du Seeder

Si vous devez lancer le seeder manuellement :

```bash
# Sur le serveur de production
cd /home/forge/votresite.com

# Lancer le seeder (avec confirmation)
php artisan db:seed --class=ProductionSeeder

# Forcer sans confirmation (DANGER - À utiliser avec précaution)
php artisan db:seed --class=ProductionSeeder --force
```

## ⚠️ Important

### Le Seeder Demande Confirmation
Par défaut, `ProductionSeeder` demande une confirmation avant de :
- Nettoyer les données de planning (teams, matches, venues, animations)
- Importer les nouvelles données depuis CSV

### Données Utilisateurs TOUJOURS Préservées
Le seeder **NE TOUCHERA JAMAIS** à :
- `users` - Comptes utilisateurs
- `predictions` - Prédictions existantes
- `point_logs` - Historique des points

### Format du CSV
Le fichier `venues.csv` doit avoir ce format :

```csv
venue_name,zone,date,time,team_1,team_2,latitude,longitude,TYPE_PDV
BAR CHEZ JEAN,THIAROYE,21/12/2025,21 H,SENEGAL,MAROC,14.7456,-17.3829,dakar
BAR KAMIEUM,THIAROYE,03/01/2026,16 H,HUITIEME DE FINALE,,14.7456,-17.3829,dakar
```

**Notes :**
- `team_2` vide = match de phase finale "à déterminer"
- `TYPE_PDV` : "dakar", "thies", etc. (par défaut: "dakar")

## 🎉 Résultat

Après déploiement :

- ✅ Production = Version locale (données de planning)
- ✅ Utilisateurs préservés à 100%
- ✅ Toutes les équipes ont leurs drapeaux (ISO codes)
- ✅ Tous les PDV ont leurs coordonnées GPS
- ✅ La carte fonctionne parfaitement
- ✅ Les matchs "à déterminer" s'affichent correctement

## 📞 Support

En cas de problème :

1. Vérifier les logs : `storage/logs/laravel.log`
2. Vérifier le CSV : `venues.csv` à la racine
3. Re-exécuter le seeder manuellement si nécessaire

---

**Version :** 1.0  
**Date :** 19 décembre 2025  
**Auteur :** Système de déploiement CAN 2025
