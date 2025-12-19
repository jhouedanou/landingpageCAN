#!/bin/bash

echo "🔧 Nettoyage des doublons d'équipes (via Docker)"
echo "================================================="
echo ""

# Nom du conteneur
CONTAINER="landingpagecan-laravel.test-1"

# Vérifier que le conteneur existe
if ! docker ps --format '{{.Names}}' | grep -q "^${CONTAINER}$"; then
    echo "❌ Conteneur $CONTAINER non trouvé"
    echo ""
    echo "💡 Conteneurs actifs :"
    docker ps --format "table {{.Names}}\t{{.Image}}"
    exit 1
fi

echo "📦 Conteneur: $CONTAINER"
echo ""

# Exécuter la commande artisan
echo "🚀 Exécution du nettoyage..."
echo "======================================"
echo ""

docker exec -it -w /app $CONTAINER php artisan teams:fix-duplicates

if [ $? -ne 0 ]; then
    echo ""
    echo "❌ Erreur lors de l'exécution"
    exit 1
fi

echo ""
echo "======================================"
echo ""

# Vérification finale
echo "🔍 Vérification finale..."
docker exec -w /app $CONTAINER php artisan tinker --execute="
\$total = \App\Models\Team::count();
\$unique = \App\Models\Team::distinct('name')->count('name');
echo 'Total équipes: ' . \$total . PHP_EOL;
echo 'Équipes uniques: ' . \$unique . PHP_EOL;
if (\$total === \$unique) {
    echo '✅ Aucun doublon! Tout est OK.' . PHP_EOL;
} else {
    echo '⚠️  Il reste ' . (\$total - \$unique) . ' doublon(s).' . PHP_EOL;
}
"

echo ""
echo "🎉 Opération terminée!"
