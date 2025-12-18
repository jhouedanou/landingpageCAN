#!/bin/bash

# Script de déploiement SOBOA FOOT TIME pour Laravel Forge
# Ce script crée une nouvelle release et déploie l'application

set -e

# Créer une nouvelle release
$CREATE_RELEASE()

# Accéder au répertoire de la nouvelle release
cd $FORGE_RELEASE_DIRECTORY

# ========== INSTALLATION DES DÉPENDANCES ==========
echo "📦 Installation des dépendances PHP..."
$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# ========== CONSTRUCTION DU FRONTEND ==========
echo "🎨 Construction du frontend..."
npm ci
npm run build

# ========== NETTOYAGE DES CACHES ==========
echo "🧹 Nettoyage des caches..."
$FORGE_PHP artisan config:clear
$FORGE_PHP artisan route:clear
$FORGE_PHP artisan view:clear
$FORGE_PHP artisan cache:clear

# ========== MIGRATIONS ET SEEDERS ==========
echo "🗄️ Suppression et recréation de la base de données..."
$FORGE_PHP artisan migrate:fresh --seed --force

# ========== OPTIMISATION ==========
echo "⚡ Optimisation de l'application..."
$FORGE_PHP artisan config:cache
$FORGE_PHP artisan route:cache
$FORGE_PHP artisan view:cache
$FORGE_PHP artisan storage:link

# Activer la nouvelle release
echo "✅ Activation de la nouvelle release..."
$ACTIVATE_RELEASE()

# Redémarrer les queues
echo "🔄 Redémarrage des queues..."
$RESTART_QUEUES()

echo "🎉 Déploiement terminé avec succès!"
echo "📍 Les points de vente au Sénégal ont été créés"
