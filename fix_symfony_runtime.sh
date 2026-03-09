#!/bin/bash

echo "==============================================="
echo "      RÉPARATION SYMFONY RUNTIME + JWT"
echo "==============================================="

echo ""
echo "🔧 1. Installation de symfony/runtime..."
echo "-----------------------------------------------"
composer require symfony/runtime

echo ""
echo "🔧 2. Vérification / installation de LexikJWT..."
echo "-----------------------------------------------"
composer require lexik/jwt-authentication-bundle

echo ""
echo "🔧 3. Vérification des permissions de bin/console..."
echo "-----------------------------------------------"
chmod +x bin/console

echo ""
echo "🧹 4. Nettoyage du cache..."
echo "-----------------------------------------------"
rm -rf var/cache/*

echo ""
echo "🔥 5. Reconstruction du cache..."
echo "-----------------------------------------------"
bin/console cache:clear --env=dev
bin/console cache:warmup --env=dev

echo ""
echo "==============================================="
echo "      SYMFONY RUNTIME + JWT RÉPARÉS"
echo "==============================================="
echo "➡️ Tu peux maintenant relancer : ./jwt_test.sh"
echo "==============================================="
