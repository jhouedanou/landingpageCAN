#!/bin/bash

echo "🔧 Nettoyage des doublons d'équipes"
echo "======================================"
echo ""

cd /Users/houedanou/Documents/landingpageCAN

# Étape 1 : Supprimer les doublons
echo "📋 Étape 1/2 : Suppression des doublons"
echo "---------------------------------------"
php fix-duplicate-teams.php

if [ $? -ne 0 ]; then
    echo "❌ Erreur lors de la suppression des doublons"
    exit 1
fi

echo ""
echo "📋 Étape 2/2 : Ajout de la contrainte d'unicité"
echo "---------------------------------------"

# Étape 2 : Appliquer la migration pour empêcher les futurs doublons
php artisan migrate --path=database/migrations/2025_12_19_174700_add_unique_constraint_to_teams_name.php

if [ $? -ne 0 ]; then
    echo "⚠️  La migration a échoué (peut-être déjà appliquée)"
fi

echo ""
echo "✅ Nettoyage terminé!"
echo ""
echo "🔍 Vérification finale..."
php artisan tinker --execute="
\$total = \App\Models\Team::count();
\$unique = \App\Models\Team::distinct('name')->count('name');
echo 'Total équipes: ' . \$total . PHP_EOL;
echo 'Équipes uniques: ' . \$unique . PHP_EOL;
if (\$total === \$unique) {
    echo '✅ Aucun doublon! Tout est OK.' . PHP_EOL;
} else {
    echo '⚠️  Il reste des doublons à traiter.' . PHP_EOL;
}
"

echo ""
echo "🎉 Opération terminée!"
