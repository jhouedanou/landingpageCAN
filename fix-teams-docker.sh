#!/bin/bash

echo "🔧 Nettoyage des doublons d'équipes (via Docker)"
echo "================================================="
echo ""

# Trouver le nom du conteneur Laravel
CONTAINER=$(docker ps --filter "name=laravel" --format "{{.Names}}" | head -1)

if [ -z "$CONTAINER" ]; then
    # Essayer avec un nom générique
    CONTAINER=$(docker ps --filter "ancestor=php" --format "{{.Names}}" | head -1)
fi

if [ -z "$CONTAINER" ]; then
    echo "❌ Aucun conteneur Docker trouvé"
    echo ""
    echo "💡 Conteneurs actifs :"
    docker ps --format "table {{.Names}}\t{{.Image}}"
    echo ""
    echo "Veuillez spécifier le nom du conteneur :"
    read CONTAINER
    
    if [ -z "$CONTAINER" ]; then
        echo "❌ Opération annulée"
        exit 1
    fi
fi

echo "📦 Conteneur détecté: $CONTAINER"
echo ""

# Copier le script dans le conteneur
echo "📋 Copie du script de nettoyage..."
docker cp fix-duplicate-teams.php $CONTAINER:/var/www/html/fix-duplicate-teams.php

if [ $? -ne 0 ]; then
    echo "❌ Erreur lors de la copie du script"
    exit 1
fi

echo "✅ Script copié"
echo ""

# Exécuter le script dans le conteneur
echo "🚀 Exécution du nettoyage..."
echo "======================================"
echo ""

docker exec -it $CONTAINER php /var/www/html/fix-duplicate-teams.php

if [ $? -ne 0 ]; then
    echo ""
    echo "❌ Erreur lors de l'exécution"
    exit 1
fi

echo ""
echo "======================================"
echo ""

# Nettoyer le script temporaire
echo "🧹 Nettoyage..."
docker exec $CONTAINER rm -f /var/www/html/fix-duplicate-teams.php

echo "✅ Nettoyage terminé!"
echo ""

# Vérification finale
echo "🔍 Vérification finale..."
docker exec $CONTAINER php artisan tinker --execute="
\$total = \App\Models\Team::count();
\$unique = \App\Models\Team::distinct('name')->count('name');
echo 'Total équipes: ' . \$total . PHP_EOL;
echo 'Équipes uniques: ' . \$unique . PHP_EOL;
if (\$total === \$unique) {
    echo '✅ Aucun doublon! Tout est OK.' . PHP_EOL;
} else {
    echo '⚠️  Il reste ' . (\$total - \$unique) . ' doublon(s) à traiter.' . PHP_EOL;
}
"

echo ""
echo "🎉 Opération terminée!"
