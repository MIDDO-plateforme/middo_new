#!/bin/bash

echo "==============================================="
echo "      RÉPARATION DES PERMISSIONS SYMFONY"
echo "==============================================="

echo ""
echo "🔧 1. Donner les bonnes permissions à bin/console..."
echo "-----------------------------------------------"
chmod +x bin/console
echo "✔️ bin/console est maintenant exécutable."

echo ""
echo "🔧 2. Donner les permissions aux scripts .sh..."
echo "-----------------------------------------------"
chmod +x *.sh 2>/dev/null
chmod +x tools/*.sh 2>/dev/null
echo "✔️ Scripts .sh rendus exécutables."

echo ""
echo "🔧 3. Permissions sur var/ (cache + logs)..."
echo "-----------------------------------------------"
chmod -R 775 var/
echo "✔️ var/ accessible par Symfony."

echo ""
echo "🔧 4. Permissions sur config/jwt..."
echo "-----------------------------------------------"
chmod 600 config/jwt/private.pem 2>/dev/null
chmod 644 config/jwt/public.pem 2>/dev/null
echo "✔️ Permissions JWT sécurisées."

echo ""
echo "🔧 5. Permissions sur vendor/..."
echo "-----------------------------------------------"
chmod -R 755 vendor/
echo "✔️ vendor/ prêt pour l'exécution."

echo ""
echo "🔧 6. Permissions sur migrations/ (si présent)..."
echo "-----------------------------------------------"
chmod -R 755 migrations/ 2>/dev/null

echo ""
echo "🔧 7. Permissions sur public/ (assets)..."
echo "-----------------------------------------------"
chmod -R 755 public/

echo ""
echo "==============================================="
echo "      PERMISSIONS SYMFONY RÉPARÉES"
echo "==============================================="
echo "➡️ Tu peux maintenant relancer : ./symfony_cache_rebuild.sh"
echo "==============================================="
