#!/bin/bash

# ==========================================
# SCRIPT DE DÉPLOIEMENT FORGE - PRODUCTION
# GAZELLE - Le goût de notre victoire
# ==========================================

$CREATE_RELEASE()

cd $FORGE_RELEASE_DIRECTORY

echo "📦 Installation des dépendances PHP..."
$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

echo "🎨 Installation et build du frontend (avec responsive fixes)..."
npm ci
npm run build

# ==========================================
# MIGRATIONS (SANS --seed global!)
# ==========================================

echo "🔄 Running migrations..."
$FORGE_PHP artisan migrate --force

# ==========================================
# PRODUCTION SEEDING (WITH LOCAL DATA)
# ==========================================
# Uses DatabaseSeeder to import data from seeders
# ✅ Preserves: ALL existing data (users, predictions, teams, matches, venues)
# 🔄 Updates: teams, matches, venues with latest data from seeders
# ⚠️  Note: Uses updateOrCreate to avoid duplicates

echo "🌱 Running PRODUCTION seeders (with local data)..."
$FORGE_PHP artisan db:seed --class=DatabaseSeeder --force

echo "🔧 Optimizing application..."
$FORGE_PHP artisan optimize

echo "🔗 Creating storage link..."
$FORGE_PHP artisan storage:link

# ==========================================
# CACHE CLEARING (FIX 404 error!)
# ==========================================

echo "🧹 Clearing caches..."
$FORGE_PHP artisan config:clear
$FORGE_PHP artisan cache:clear
$FORGE_PHP artisan view:clear
$FORGE_PHP artisan route:clear  # ← CRITICAL: Fixes 404 on "modifier" link

$ACTIVATE_RELEASE()

$RESTART_QUEUES()

echo "✅ Deployment completed successfully!"
