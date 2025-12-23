#!/bin/bash

# ==========================================
# SCRIPT DE DÉPLOIEMENT FORGE - PRODUCTION
# GAZELLE - Le goût de notre victoire
# ==========================================

$CREATE_RELEASE()

cd $FORGE_RELEASE_DIRECTORY

echo "📦 Installation des dépendances PHP..."
$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

echo "🎨 Installation et build du frontend..."
npm ci
npm run build

# ==========================================
# MIGRATIONS
# ==========================================

echo "🔄 Running migrations..."
$FORGE_PHP artisan migrate --force

# ==========================================
# PRODUCTION SEEDING - DÉSACTIVÉ
# ==========================================
# ⚠️ Le ProductionSeeder réinitialise les données de planning
# Ne pas l'exécuter automatiquement car il supprime les animations
# ajoutées manuellement en production.
#
# Pour synchroniser manuellement depuis le CSV local:
# php artisan db:seed --class=ProductionSeeder --force
# ==========================================

# echo "🌱 Running PRODUCTION seeders..."
# $FORGE_PHP artisan db:seed --class=ProductionSeeder --force

# ==========================================
# CACHE CLEARING (CRITICAL - avant optimize!)
# ==========================================

echo "🧹 Clearing ALL caches..."
$FORGE_PHP artisan config:clear
$FORGE_PHP artisan cache:clear
$FORGE_PHP artisan view:clear
$FORGE_PHP artisan route:clear
$FORGE_PHP artisan event:clear

echo "🔧 Optimizing application..."
$FORGE_PHP artisan optimize

echo "🔗 Creating storage link..."
$FORGE_PHP artisan storage:link

$ACTIVATE_RELEASE()

$RESTART_QUEUES()

echo "✅ Deployment completed successfully!"