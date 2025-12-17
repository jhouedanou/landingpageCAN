# �배 Guide de Déploiement SOBOA FOOT TIME

## Vue d'ensemble

Ce document décrit le processus de déploiement de SOBOA FOOT TIME sur Laravel Forge.

## Architecture de déploiement

### 1. **Script de déploiement (`deploy.sh`)**

Le script `deploy.sh` exécute les étapes suivantes sur Forge:

```bash
$CREATE_RELEASE()                    # Crée une nouvelle release
cd $FORGE_RELEASE_DIRECTORY

# Dépendances PHP
$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Frontend (Vue/React/etc)
npm ci
npm run build

# Base de données
$FORGE_PHP artisan migrate --force --seed

# Optimisation
$FORGE_PHP artisan optimize
$FORGE_PHP artisan storage:link

$ACTIVATE_RELEASE()                 # Active la nouvelle release
$RESTART_QUEUES()                   # Redémarre les files d'attente
```

### 2. **Seeders pour l'initialisation des données**

Les données de production sont initialisées via les seeders Laravel:

#### Données seeded automatiquement:

| Seeder | Contenu | Enregistrements |
|--------|---------|-----------------|
| **TeamSeeder** | Équipes participantes | ~32 équipes |
| **StadiumSeeder** | Stades du tournoi | ~12 stades |
| **MatchSeeder** | Matches de groupe | 13 matches |
| **BarSeeder** | Points de vente partenaires | 18+ lieux |

#### Arborescence des seeders:

```
database/seeders/
├── DatabaseSeeder.php           # Point d'entrée principal
├── ProductionDataSeeder.php     # Seeder de production (optionnel)
├── TeamSeeder.php               # Équipes
├── StadiumSeeder.php            # Stades
├── MatchSeeder.php              # Matches
├── BarSeeder.php                # Points de vente
├── UserSeeder.php               # Utilisateurs de test
├── PredictionSeeder.php         # Pronostics de test
└── AdminUserSeeder.php          # Utilisateur admin
```

## Processus de déploiement

### Via Forge Dashboard

1. **Configurer le script de déploiement:**
   - Aller à Site → [votre site] → Deploy
   - Copier le contenu de `deploy.sh`
   - Coller dans la zone "Deploy Script"
   - Sauvegarder

2. **Déclencher le déploiement:**
   - Cliquer sur "Deploy Now"
   - Le script s'exécutera automatiquement

### Via Git Webhook

1. Chaque push vers la branche de déploiement (`main` ou `production`)
2. Forge déclenche automatiquement le script de déploiement
3. Les étapes s'exécutent dans l'ordre défini

## Données seeded

### Matches

- **Format**: 13 matches de groupe
- **Données**: Équipes, dates, stades, groupes
- **Source**: `database/seeders/MatchSeeder.php`

```php
['home' => 'Maroc', 'away' => 'Comores', 'date' => '2025-12-21 20:00:00', 'grp' => 'A', ...]
```

### Points de vente

- **Nombre**: 18+ lieux à Abidjan et environs
- **Zones**: Cocody, Plateau, Marcory, Yopougon, Treichville, etc.
- **Rayon géofencing**: 200 mètres
- **Source**: `database/seeders/BarSeeder.php`

### Équipes

- **Nombre**: 32 équipes africaines
- **Source**: `database/seeders/TeamSeeder.php`

### Stades

- **Nombre**: 12 stades au Maroc
- **Source**: `database/seeders/StadiumSeeder.php`

## Exécution manuelle des seeders

### Exécuter tous les seeders

```bash
php artisan migrate:refresh --seed
```

### Exécuter un seeder spécifique

```bash
php artisan db:seed --class=MatchSeeder
php artisan db:seed --class=BarSeeder
php artisan db:seed --class=ProductionDataSeeder
```

### Sans afficher les données de test

```bash
# Seeders sans users/predictions de test
php artisan db:seed --class=ProductionDataSeeder
```

## Variables d'environnement Forge

Certaines variables sont remplacées automatiquement par Forge:

- `$CREATE_RELEASE()` - Crée une nouvelle release
- `$FORGE_RELEASE_DIRECTORY` - Chemin de la release
- `$FORGE_COMPOSER` - Commande composer
- `$FORGE_PHP` - Commande PHP
- `$ACTIVATE_RELEASE()` - Active la release
- `$RESTART_QUEUES()` - Redémarre les queues

## Gestion des données en production

### Backup avant migration

```bash
# Créer un backup (Forge fait cela automatiquement)
mysqldump -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE > backup.sql
```

### Rollback si nécessaire

```bash
# Reverser les seeders
php artisan migrate:rollback

# Restaurer le backup
mysql -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE < backup.sql
```

## Checklist de déploiement

- [ ] Tester le script en local: `php artisan migrate:refresh --seed`
- [ ] Vérifier que `npm run build` fonctionne
- [ ] Configurer le script dans Forge
- [ ] Vérifier les logs de déploiement
- [ ] Tester l'application en production
- [ ] Vérifier les données seeded:
  - [ ] Matches affichés
  - [ ] Points de vente visibles
  - [ ] Utilisateur admin créé

## Dépannage

### Les seeders ne s'exécutent pas

1. Vérifier la syntaxe du script Forge
2. Vérifier les logs: `tail -f storage/logs/laravel.log`
3. Exécuter manuellement: `$FORGE_PHP artisan migrate --seed`

### Erreur: "Table already exists"

- Utiliser `--force` flag: `artisan migrate --force --seed`
- Les seeders utilisent `updateOrCreate()` pour éviter les doublons

### Base de données vide après déploiement

1. Vérifier que les migrations ont s'exécuté
2. Vérifier que `--seed` est présent dans la commande migrate
3. Vérifier les permissions de la base de données

## Architecture des seeders

```
DatabaseSeeder.php
├── TeamSeeder        (32 équipes)
├── StadiumSeeder     (12 stades)
├── MatchSeeder       (13 matches)
├── UserSeeder        (utilisateurs de test)
├── BarSeeder         (18+ points de vente)
├── PredictionSeeder  (pronostics de test)
└── AdminUserSeeder   (admin)
```

**Pour production uniquement**, utiliser:

```bash
php artisan db:seed --class=ProductionDataSeeder
```

Cela exécute uniquement les seeders de données essentielles (équipes, stades, matches, lieux).

## Support

Pour toute question sur le déploiement:
- Consulter les logs Forge
- Vérifier la documentation Laravel Forge
- Contacter le support SOBOA FOOT TIME
