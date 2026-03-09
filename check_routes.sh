#!/bin/bash

echo "==============================================="
echo "      VERIFICATION DES ROUTES SYMFONY"
echo "==============================================="

echo ""
echo "Verification du kernel..."
echo "-----------------------------------------------"

if [ ! -f "config/bundles.php" ]; then
    echo "Fichier config/bundles.php manquant."
    exit 1
fi

echo "Kernel OK."

echo ""
echo "Liste des routes..."
echo "-----------------------------------------------"

bin/console debug:router || echo "Impossible d'afficher les routes."

echo ""
echo "==============================================="
echo "      VERIFICATION DES ROUTES TERMINEE"
echo "==============================================="
