#!/bin/bash

# ==========================================
# SCRIPT DE DÉPLOIEMENT FORGE - PRODUCTION
# ==========================================

$CREATE_RELEASE()

cd $FORGE_RELEASE_DIRECTORY

# Installation des dépendances PHP
$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Installation et build du frontend
npm ci
npm run build

# ==========================================
# MIGRATIONS ET SEEDERS
# ==========================================

echo "🔄 Running migrations..."
$FORGE_PHP artisan migrate --force

echo "🌍 Seeding Teams (équipes nationales)..."
$FORGE_PHP artisan db:seed --class=TeamSeeder --force

echo "🏟️ Seeding Stadiums (stades)..."
$FORGE_PHP artisan db:seed --class=StadiumSeeder --force

echo "⚽ Seeding Matches (matchs de la CAN)..."
$FORGE_PHP artisan db:seed --class=MatchSeeder --force

echo "📍 Fixing Venues & Animations (60 PDV + coordonnées + liens)..."
$FORGE_PHP artisan db:seed --class=FixAnimationsSeeder --force

echo "🔧 Optimizing application..."
$FORGE_PHP artisan optimize

echo "🔗 Creating storage link..."
$FORGE_PHP artisan storage:link

echo "🧹 Clearing caches..."
$FORGE_PHP artisan config:clear
$FORGE_PHP artisan cache:clear
$FORGE_PHP artisan view:clear
$FORGE_PHP artisan route:clear

$ACTIVATE_RELEASE()

$RESTART_QUEUES()

echo "✅ Deployment completed successfully!"
