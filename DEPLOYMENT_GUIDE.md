# Guide de Déploiement - GAZELLE CAN 2025

## Vue d'ensemble

Ce guide explique comment déployer une version "fraîche" de l'application avec les nouvelles données du planning tout en préservant les données utilisateurs.

## Seeders Disponibles

### 1. FreshDeploymentSeeder (RECOMMANDÉ pour déploiement)
**Fichier:** `database/seeders/FreshDeploymentSeeder.php`

**Usage:**
```bash
php artisan db:seed --class=FreshDeploymentSeeder --force
```

**Caractéristiques:**
- ✅ **Préserve:** Utilisateurs (users table)
- 🔄 **Rafraîchit:** Teams, Matches, Venues, Animations (depuis venues.csv)
- ⚠️  **Supprime:** Predictions (seront recréées par les utilisateurs)
- ✅ **Production-safe:** Peut être exécuté plusieurs fois
- ✅ **Source de données:** Fichier `venues.csv` à la racine du projet

**Quand l'utiliser:**
- Déploiement de nouvelles données de planning depuis le CSV
- Mise à jour du calendrier des matchs
- Ajout/modification de venues
- Réinitialisation pour une nouvelle saison/tournoi

### 2. VenuesSeeder (Pour développement/test)
**Fichier:** `database/seeders/VenuesSeeder.php`

**Usage:**
```bash
php artisan db:seed --class=VenuesSeeder
```

**Caractéristiques:**
- ⚠️  **DESTRUCTIF:** Supprime TOUTES les données (users, predictions, etc.)
- 🔄 **Rafraîchit:** Tout depuis venues.csv
- ⚠️  **Attention:** À n'utiliser qu'en développement/test

### 3. ProductionSafeSeeder (Ancien - conservé pour compatibilité)
**Fichier:** `database/seeders/ProductionSafeSeeder.php`

**Utilisation:** Ancienne méthode, utilise des données hardcodées dans les seeders individuels

## Script de Déploiement

Le script `forge-deployment-script.sh` est configuré pour utiliser `FreshDeploymentSeeder`.

### Processus de déploiement:

1. **Installation des dépendances**
   ```bash
   composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
   ```

2. **Build du frontend**
   ```bash
   npm ci
   npm run build
   ```

3. **Migrations**
   ```bash
   php artisan migrate --force
   ```

4. **Seeding (Fresh Deployment)**
   ```bash
   php artisan db:seed --class=FreshDeploymentSeeder --force
   ```

5. **Optimisation**
   ```bash
   php artisan optimize
   php artisan storage:link
   ```

6. **Nettoyage du cache**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   php artisan route:clear
   ```

## Format du fichier venues.csv

Le fichier `venues.csv` doit être à la racine du projet avec le format suivant:

```csv
venue_name,zone,date,time,match_name,latitude,longitude,TYPE_PDV
CHEZ JEAN,THIAROYE,23/12/2025,15 H,SENEGAL VS BOTSWANA,14.7517342,-17.381228,dakar
BAR ALLIANCE,KEUR MBAYE FALL,03/01/2026,16 H,HUITIEME DE FINALE,14.7407892,-17.3234235,
```

### Colonnes:
- **venue_name:** Nom du bar/venue
- **zone:** Zone géographique
- **date:** Date du match (format: DD/MM/YYYY)
- **time:** Heure du match (format: HH H)
- **match_name:**
  - Pour matchs réguliers: "EQUIPE1 VS EQUIPE2"
  - Pour playoffs: "HUITIEME DE FINALE", "QUART DE FINALE", etc.
- **latitude:** Coordonnée GPS latitude
- **longitude:** Coordonnée GPS longitude
- **TYPE_PDV:** Type de point de vente (dakar, regions, chr, fanzone) - optionnel, par défaut "dakar"

## Migrations Importantes

Assurez-vous que ces migrations sont présentes:

1. **add_match_name_to_matches_table:** Ajoute la colonne `match_name` pour les matchs de playoffs
2. **add_type_pdv_to_bars_table:** Ajoute la colonne `type_pdv` aux venues
3. **create_teams_table:** Avec colonnes `iso_code` et `group` nullables
4. **create_animations_table:** Pour les liens match-venue

## Commandes Utiles

### Tester le seeder en local:
```bash
php artisan db:seed --class=FreshDeploymentSeeder
```

### Réinitialiser complètement la base (développement uniquement):
```bash
php artisan migrate:fresh
php artisan db:seed --class=FreshDeploymentSeeder
```

### Vérifier les données importées:
```bash
php artisan tinker
>>> App\Models\Team::count()
>>> App\Models\Bar::count()
>>> App\Models\MatchGame::count()
>>> App\Models\Animation::count()
>>> App\Models\User::count()
```

## Checklist de Déploiement

Avant de déployer:

- [ ] Le fichier `venues.csv` est à jour à la racine du projet
- [ ] Les migrations sont testées en local
- [ ] Le seeder `FreshDeploymentSeeder` fonctionne en local
- [ ] Le script `forge-deployment-script.sh` est à jour
- [ ] Backup de la base de données de production (si nécessaire)

Après le déploiement:

- [ ] Vérifier que les utilisateurs sont préservés
- [ ] Vérifier que les nouvelles venues sont visibles
- [ ] Vérifier que les matchs sont importés correctement
- [ ] Tester la création de predictions sur les nouveaux matchs
- [ ] Vérifier la carte avec les nouvelles coordonnées GPS

## Dépannage

### Erreur "CSV file not found"
- Assurez-vous que le fichier `venues.csv` est bien à la racine du projet
- Vérifiez les permissions du fichier

### Erreur "Column not found"
- Exécutez `php artisan migrate --force` avant le seeding
- Vérifiez que toutes les migrations sont appliquées

### Les données utilisateurs ont été supprimées
- ⚠️  Cela ne devrait PAS arriver avec `FreshDeploymentSeeder`
- Si c'est le cas, vérifiez que vous utilisez le bon seeder
- Restaurez depuis un backup si nécessaire

### Nombre de matchs incorrects
- Vérifiez le format du fichier CSV
- Vérifiez qu'il n'y a pas de doublons dans le CSV
- Les matchs sont dédupliqués par date + équipes

## Support

Pour toute question ou problème, vérifiez:
1. Les logs Laravel: `storage/logs/laravel.log`
2. Les migrations appliquées: `php artisan migrate:status`
3. Le contenu du CSV est valide

---

**Dernière mise à jour:** 19 Décembre 2025
**Version:** 1.0
