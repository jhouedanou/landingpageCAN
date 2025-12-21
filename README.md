# SOBOA Grande Fête du Foot Africain

Application web de pronostics pour la Grande Fête du Foot Africain 2025.

## Installation

```bash
# Cloner le projet
git clone https://github.com/jhouedanou/landingpageCAN.git
cd landingpageCAN

# Installer les dépendances
composer install

# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate

# Lancer avec Docker
docker compose up -d

# Exécuter les migrations
docker compose exec laravel.test bash -c "cd /app && php artisan migrate --force"

# Seeder les équipes et matchs
docker compose exec laravel.test bash -c "cd /app && php artisan db:seed --class=TeamSeeder --force"
docker compose exec laravel.test bash -c "cd /app && php artisan db:seed --class=MatchSeeder --force"
```

## Configuration Firebase (Authentification SMS)

Pour activer l'authentification par SMS, ajoutez ces variables à votre fichier `.env` :

```env
FIREBASE_API_KEY=votre_api_key
FIREBASE_PROJECT_ID=votre_project_id
```

### Obtenir les clés Firebase :

1. Allez sur [Firebase Console](https://console.firebase.google.com/)
2. Créez un projet ou sélectionnez un projet existant
3. Activez **Authentication** > **Sign-in method** > **Phone**
4. Dans **Project Settings** > **General**, copiez :
   - `apiKey` → `FIREBASE_API_KEY`
   - `projectId` → `FIREBASE_PROJECT_ID`

## Dashboard Administrateur

Accédez au dashboard admin à `/admin` pour :
- Gérer les matchs (scores, statuts)
- Voir les utilisateurs et leurs points
- Déclencher le calcul des points

⚠️ **Accès admin** : L'utilisateur doit avoir `role = 'admin'` dans la table `users`.

```sql
UPDATE users SET role = 'admin' WHERE phone_number = '+225XXXXXXXXXX';
```

## Système de Points

| Action | Points |
|--------|--------|
| Participation (pronostic) | +1 |
| Bon vainqueur | +3 |
| Score exact | +3 |
| Visite lieu partenaire | +4/jour |

**Maximum par match : 7 points**

## URLs

- `/` - Accueil
- `/matches` - Liste des matchs et pronostics
- `/leaderboard` - Classement
- `/map` - Lieux partenaires
- `/dashboard` - Tableau de bord utilisateur
- `/admin` - Dashboard administrateur

## Tech Stack

- Laravel 11
- Tailwind CSS
- Alpine.js
- Firebase Auth (SMS)
- MySQL


## Déploiement et Gestion de la Base de Données

### 🚀 Déploiement sur Laravel Forge (RECOMMANDÉ)

Le déploiement utilise le script `forge-deployment-script.sh` qui :
- ✅ Installe les dépendances PHP et frontend
- ✅ Exécute les migrations
- ✅ **NOUVEAU:** Exécute les seeders pour importer toutes les données locales
- ✅ Optimise l'application
- ✅ Nettoie tous les caches

#### Configuration dans Laravel Forge

1. Allez dans votre site sur Forge
2. Cliquez sur **"Deployment"** dans le menu
3. Collez le contenu de `forge-deployment-script.sh` dans le script de déploiement
4. Cliquez sur **"Deploy Now"**

#### Données qui seront importées en production

Le seeder `DatabaseSeeder` importe automatiquement :
- **24 équipes** de la CAN 2025 (avec codes ISO et groupes)
- **9 stades** au Maroc (avec coordonnées GPS)
- **62-64 bars/points de vente** au Sénégal (avec coordonnées GPS)
- **10 matchs** (5 matchs de poules + 5 phases finales)
  - Sénégal vs Botswana (23/12/2025)
  - Afrique du Sud vs Égypte (26/12/2025)
  - Sénégal vs RD Congo (27/12/2025)
  - Côte d'Ivoire vs Cameroun (28/12/2025)
  - Sénégal vs Bénin (30/12/2025)
  - Huitième de finale, Quart de finale, Demi finale, 3ème place, Finale
- **80 animations** (liens match-bar indiquant où regarder chaque match)
- **1 utilisateur admin** (numéro configuré dans AdminUserSeeder)

**Important:**
- Le script utilise `updateOrCreate` pour éviter les doublons
- Il ne supprime JAMAIS les données existantes (users, predictions, etc.)
- **Les utilisateurs de test ne sont créés qu'en environnement local/development**
- **Les animations** permettent de savoir quels matchs sont diffusés dans quels bars

#### Vérifier les données en production

Après le déploiement, vous pouvez vérifier les données via SSH sur Forge :

```bash
# Connexion SSH sur Forge
ssh forge@votre-serveur.com

# Aller dans le répertoire de l'application
cd /home/forge/votre-site.com

# Vérifier les données
php artisan tinker --execute="
echo 'Teams: ' . \App\Models\Team::count() . PHP_EOL;
echo 'Stadiums: ' . \App\Models\Stadium::count() . PHP_EOL;
echo 'Bars: ' . \App\Models\Bar::count() . PHP_EOL;
echo 'Matches: ' . \App\Models\MatchGame::count() . PHP_EOL;
echo 'Animations: ' . \App\Models\Animation::count() . PHP_EOL;
echo 'Users: ' . \App\Models\User::count() . PHP_EOL;
"
```

#### Résultat attendu après déploiement

```
Teams: 24
Stadiums: 9
Bars: 62-64
Matches: 10
Animations: 80
Users: 1 (admin seulement, pas d'utilisateurs de test en production)
```

Les **animations** sont les liens qui indiquent quels matchs sont diffusés dans quels bars. Par exemple, le match "Sénégal vs Botswana" est diffusé dans 16 bars différents.

### Scripts de déploiement disponibles

#### 1. Reset complet de la production (⚠️ ATTENTION)

##### Pour Laravel Forge (RECOMMANDÉ) 🚀

Si vous utilisez Laravel Forge pour le déploiement :

```bash
# Tester d'abord la connexion
./test-production-connection.sh

# Puis lancer le reset
./reset-production-forge.sh
```

**Ce script va :**
- ✅ Créer une sauvegarde de la production (sur Forge + local)
- ✅ Exporter vos données locales (Docker Sail)
- ✅ Uploader vers Forge via SSH
- ✅ Importer en production (ÉCRASE TOUT)
- ✅ Nettoyer les caches Laravel automatiquement
- ✅ Vérifier l'import

📖 **Documentation Forge** : Voir [FORGE_RESET_GUIDE.md](./FORGE_RESET_GUIDE.md)

##### Pour serveur générique

Pour autres environnements (VPS, serveur dédié, etc.) :

```bash
./reset-production-database.sh
```

📖 **Documentation complète** : Voir [RESET_PRODUCTION_GUIDE.md](./RESET_PRODUCTION_GUIDE.md)

#### 2. Synchronisation sélective

Pour plus de contrôle, utilisez le script interactif :

```bash
./sync-database.sh
```

Options disponibles :
- Backup local/production
- Sync complète
- Sync sécurisée (préserve users)
- Sync données uniquement (teams, matchs, PDV)
- Comparaison local vs production

#### 3. Déploiement complet (code + base)

Pour déployer code ET base de données :

```bash
./deploy-production.sh
```

### Commandes manuelles sur Production

Si vous préférez exécuter manuellement :

```bash
# Sur le serveur de production
cd /home/forge/votre-site.com && \
php artisan migrate --force && \
php artisan db:seed --class=DatabaseSeeder --force && \
php artisan optimize && \
php artisan cache:clear && \
php artisan config:clear && \
php artisan view:clear && \
php artisan route:clear && \
echo "✅ Synchronisation terminée!"
```

### Configuration pour la production

Créez un fichier `.env.production` (déjà dans .gitignore) :

```bash
cp .env.production.example .env.production
# Puis éditez avec vos vraies valeurs
```