#!/bin/bash
set -e

# ==========================================
# DÉPLOIEMENT FORGE — SOBOA FOOT TIME
# CODE + MIGRATIONS (ne touche PAS aux données existantes)
# ==========================================

$CREATE_RELEASE()
cd $FORGE_RELEASE_DIRECTORY

echo "📦 Dépendances PHP..."
$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

echo "🎨 Build frontend..."
npm ci
npm run build

echo "🔄 Migrations..."
# migrate --force applique UNIQUEMENT les migrations en attente, une seule fois
# (suivi dans la table `migrations`). Les nouvelles migrations 2026_06_15_*
# (point_logs.adjustment + predictions.bar_id) s'appliquent donc au prochain
# déploiement et ne sont jamais rejouées ensuite. Additif : ne touche pas aux données.
$FORGE_PHP artisan migrate --force

# ⚠️ NE JAMAIS mettre season:reset ou db:seed ici (wipe à chaque deploy).
# FreshDeploymentSeeder / deploy-production.sh truncate matches => détruisent
# pronostics ET commentaires. Reset compétition = UNE fois, manuellement.

echo "🧹 Clear caches..."
$FORGE_PHP artisan optimize:clear

echo "🔧 Optimize..."
$FORGE_PHP artisan optimize

echo "🔗 Storage link..."
$FORGE_PHP artisan storage:link || true

$ACTIVATE_RELEASE()
$RESTART_QUEUES()

echo "✅ Déploiement terminé."
