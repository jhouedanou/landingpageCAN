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

# ========== MIGRATIONS ET SEEDERS ==========
echo "🗄️ Exécution des migrations et seeders..."
$FORGE_PHP artisan migrate --force --seed

# ========== OPTIMISATION ==========
echo "⚡ Optimisation de l'application..."
$FORGE_PHP artisan optimize
$FORGE_PHP artisan storage:link

# Activer la nouvelle release
echo "✅ Activation de la nouvelle release..."
$ACTIVATE_RELEASE()

# Redémarrer les queues
echo "🔄 Redémarrage des queues..."
$RESTART_QUEUES()

echo "🎉 Déploiement terminé avec succès!"
