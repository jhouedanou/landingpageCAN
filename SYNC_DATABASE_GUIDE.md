# 📚 GUIDE DE SYNCHRONISATION ET DÉPLOIEMENT

## 🎯 Vue d'Ensemble

Ce guide explique comment synchroniser votre base de données locale vers la production et déployer l'application complète.

## 🛠️ Outils Disponibles

### 1. Script Bash de Synchronisation (`sync-database.sh`)
Script shell interactif pour gérer les backups et synchronisations.

### 2. Seeder Laravel (`ProductionSyncSeeder.php`)
Seeder pour export/import granulaire des données.

### 3. Commande Artisan (`SyncDatabase.php`)
Commande Laravel intégrée pour la gestion de base de données.

### 4. Script de Déploiement Complet (`deploy-production.sh`)
Script automatisé pour déploiement code + base de données.

---

## 📋 MÉTHODES DE SYNCHRONISATION

### Méthode 1: Via Script Bash (Recommandé)

```bash
# Rendre le script exécutable
chmod +x sync-database.sh

# Lancer le script interactif
./sync-database.sh
```

**Options disponibles:**
1. **Backup local** - Sauvegarde la base locale
2. **Backup production** - Sauvegarde la base production
3. **Sync COMPLET** - Écrase toute la base production
4. **Sync SAFE** - Préserve users et predictions
5. **Sync DONNÉES** - Teams, matchs, PDV uniquement
6. **Comparer** - Compare local vs production

### Méthode 2: Via Commande Artisan

```bash
# Backup local
docker compose exec laravel.test php artisan db:sync backup

# Backup production
docker compose exec laravel.test php artisan db:sync backup --env=production

# Synchronisation sécurisée
docker compose exec laravel.test php artisan db:sync sync --safe

# Comparaison
docker compose exec laravel.test php artisan db:sync compare
```

### Méthode 3: Via Seeder (Pour données spécifiques)

```bash
# Sur local: Export
docker compose exec laravel.test php artisan db:seed --class=ProductionSyncSeeder --export

# Copier le fichier sur production
scp storage/app/production_sync.json user@server:/path/to/app/storage/app/

# Sur production: Import
php artisan db:seed --class=ProductionSyncSeeder --import --force
```

---

## 🚀 DÉPLOIEMENT COMPLET

### Configuration Préalable

1. **Créer `.env.production`** avec les credentials de production:
```env
DB_HOST=your-production-host
DB_PORT=3306
DB_DATABASE=soboa_foot_time
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

2. **Configurer les variables d'environnement**:
```bash
export PRODUCTION_HOST="your-server.com"
export PRODUCTION_USER="forge"
export PRODUCTION_PATH="/home/forge/soboa-foot-time"
export PRODUCTION_BRANCH="main"
```

### Lancer le Déploiement

```bash
# Rendre le script exécutable
chmod +x deploy-production.sh

# Lancer le déploiement complet
./deploy-production.sh
```

Le script va:
1. ✅ Vérifier les prérequis
2. 📦 Créer des backups de sécurité
3. 📤 Pousser le code vers Git
4. 🚀 Déployer sur le serveur
5. 🔄 Synchroniser la base de données (optionnel)
6. 🧪 Exécuter des tests post-déploiement
7. 📊 Afficher un résumé

---

## 💾 TYPES DE SYNCHRONISATION

### 1. Synchronisation COMPLÈTE ⚠️
- **Écrase TOUTE la base de production**
- Inclut users, predictions, points
- À utiliser uniquement pour une réinitialisation totale

```bash
./sync-database.sh
# Choisir option 3: Sync COMPLET
```

### 2. Synchronisation SÉCURISÉE 🛡️
- **Préserve les utilisateurs et leurs données**
- Synchronise: teams, matches, venues, animations
- Recommandé pour les mises à jour de planning

```bash
./sync-database.sh
# Choisir option 4: Sync SAFE
```

### 3. Synchronisation DONNÉES 📊
- Utilise le seeder Laravel
- Plus granulaire et contrôlé
- Idéal pour des mises à jour ciblées

```bash
./sync-database.sh
# Choisir option 5: Sync DONNÉES
```

---

## 📁 STRUCTURE DES BACKUPS

Les backups sont stockés dans `storage/backups/` avec la nomenclature:
- `local_backup_YYYYMMDD_HHMMSS.sql` - Backups locaux
- `production_backup_YYYYMMDD_HHMMSS.sql` - Backups production
- `sync_export_YYYYMMDD_HHMMSS.sql` - Exports pour sync
- `production_sync.json` - Export JSON du seeder

---

## ⚡ COMMANDES RAPIDES

### Backup Rapide
```bash
# Local
docker compose exec laravel.test php artisan db:backup

# Production (via SSH)
ssh user@server "cd /path/to/app && php artisan db:backup"
```

### Sync Rapide (Données uniquement)
```bash
# Export local
docker compose exec laravel.test bash -c "cd /app && \
  php artisan db:seed --class=ProductionSyncSeeder --export"

# Import production (après upload)
ssh user@server "cd /path/to/app && \
  php artisan db:seed --class=ProductionSyncSeeder --import --force"
```

### Vérification Post-Sync
```bash
# Comparer les statistiques
docker compose exec laravel.test php artisan db:sync compare

# Vérifier l'intégrité
ssh user@server "cd /path/to/app && php artisan tinker --execute='
  echo \"Users: \" . \App\Models\User::count();
  echo \" Teams: \" . \App\Models\Team::count();
  echo \" Matches: \" . \App\Models\MatchGame::count();
'"
```

---

## 🔒 SÉCURITÉ

### Règles Importantes

1. **TOUJOURS faire un backup avant synchronisation**
2. **Tester d'abord en environnement de staging**
3. **Vérifier les données après synchronisation**
4. **Garder les 10 derniers backups minimum**

### Restauration d'Urgence

Si quelque chose tourne mal:

```bash
# Identifier le backup à restaurer
ls -la storage/backups/

# Restaurer en production
ssh user@server "cd /path/to/app && \
  mysql -u DB_USER -p DB_NAME < storage/backups/production_backup_TIMESTAMP.sql"

# Vider les caches
ssh user@server "cd /path/to/app && \
  php artisan cache:clear && \
  php artisan config:clear"
```

---

## 📊 CAS D'USAGE

### Cas 1: Mise à jour du calendrier des matchs
```bash
./sync-database.sh
# Option 4: Sync SAFE
```

### Cas 2: Ajout de nouveaux PDV
```bash
# Via seeder pour plus de contrôle
docker compose exec laravel.test php artisan db:seed --class=BarSeeder --force
```

### Cas 3: Réinitialisation complète (nouvelle saison)
```bash
./sync-database.sh
# Option 3: Sync COMPLET
# ⚠️ Confirmer plusieurs fois
```

### Cas 4: Correction de données spécifiques
```bash
# Utiliser le seeder avec export/import
docker compose exec laravel.test php artisan db:seed --class=ProductionSyncSeeder
```

---

## 🐛 DÉPANNAGE

### Erreur: "Access denied"
```bash
# Vérifier les credentials dans .env.production
cat .env.production

# Tester la connexion
mysql -h HOST -u USER -p DATABASE
```

### Erreur: "Command not found: mysqldump"
```bash
# Installer MySQL client
# Mac
brew install mysql-client

# Ubuntu/Debian
sudo apt-get install mysql-client

# Via Docker
docker compose exec mysql mysqldump ...
```

### Erreur: "SSH connection refused"
```bash
# Vérifier la clé SSH
ssh-add -l

# Tester la connexion
ssh -v user@server
```

### Base corrompue après sync
```bash
# Restaurer immédiatement le backup
ssh user@server "cd /path && \
  mysql -u USER -p DB < storage/backups/production_backup_latest.sql"
```

---

## 📅 PLANNING DE SYNCHRONISATION

### Quotidien
- Backup automatique de production (via cron)

### Hebdomadaire
- Sync des données de planning (matches, PDV)
- Backup complet local et production

### Mensuel
- Nettoyage des anciens backups
- Vérification de l'intégrité des données

### Exemple de Cron
```bash
# Backup quotidien à 3h du matin
0 3 * * * cd /home/forge/soboa-foot-time && php artisan db:backup

# Sync hebdomadaire le dimanche à 2h
0 2 * * 0 cd /home/forge/soboa-foot-time && php artisan db:seed --class=ProductionSyncSeeder --import
```

---

## 📝 CHECKLIST PRÉ-DÉPLOIEMENT

- [ ] Backup local créé
- [ ] Backup production créé
- [ ] Code testé localement
- [ ] Migrations vérifiées
- [ ] Variables d'environnement configurées
- [ ] Accès SSH vérifié
- [ ] Espace disque suffisant
- [ ] Maintenance planifiée annoncée

---

## 🆘 CONTACTS D'URGENCE

En cas de problème critique:

1. **Restaurer le dernier backup fonctionnel**
2. **Activer le mode maintenance**: `php artisan down`
3. **Vérifier les logs**: `tail -f storage/logs/laravel.log`
4. **Contacter l'équipe DevOps**

---

## 📈 MONITORING POST-SYNC

Après chaque synchronisation, vérifier:

1. **Nombre d'utilisateurs** inchangé (si sync safe)
2. **Predictions** préservées
3. **Points** cohérents
4. **Matchs** correctement importés
5. **Animations** liées aux bons PDV
6. **Performance** de l'application

```bash
# Script de vérification
ssh user@server "cd /path && php artisan tinker --execute='
  \$stats = [
    \"Users\" => \App\Models\User::count(),
    \"Teams\" => \App\Models\Team::count(),
    \"Matches\" => \App\Models\MatchGame::count(),
    \"Venues\" => \App\Models\Bar::count(),
    \"Animations\" => \App\Models\Animation::count(),
    \"Predictions\" => \App\Models\Prediction::count(),
  ];
  print_r(\$stats);
'"
```

---

✅ **Fin du guide de synchronisation et déploiement**
