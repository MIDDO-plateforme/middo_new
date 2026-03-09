#!/bin/bash

echo "==============================================="
echo "      RECONSTRUCTION DU CACHE SYMFONY"
echo "==============================================="

echo ""
echo "🧹 1. Suppression du cache Symfony..."
echo "-----------------------------------------------"
rm -rf var/cache/*

echo ""
echo "🔧 2. Reconstruction du cache en environnement dev..."
echo "-----------------------------------------------"
bin/console cache:clear --env=dev
bin/console cache:warmup --env=dev

echo ""
echo "🔧 3. Reconstruction du cache en environnement prod..."
echo "-----------------------------------------------"
bin/console cache:clear --env=prod
bin/console cache:warmup --env=prod

echo ""
echo "🔍 4. Vérification du container Symfony..."
echo "-----------------------------------------------"
bin/console lint:container || echo "⚠️ Erreurs dans le container Symfony."

echo ""
echo "🔍 5. Vérification des routes..."
echo "-----------------------------------------------"
bin/console lint:routes || echo "⚠️ Erreurs dans les routes Symfony."

echo ""
echo "==============================================="
echo "      CACHE SYMFONY RECONSTRUIT"
echo "==============================================="
