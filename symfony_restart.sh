#!/bin/bash

echo "==============================================="
echo "      REDÉMARRAGE COMPLET DU SERVEUR SYMFONY"
echo "==============================================="

echo ""
echo "🛑 1. Arrêt du serveur Symfony (si actif)..."
echo "-----------------------------------------------"
pkill -f "symfony server" 2>/dev/null
pkill -f "php -S" 2>/dev/null
echo "✔️ Serveur arrêté (ou déjà inactif)."

echo ""
echo "🔧 2. Vérification des permissions de bin/console..."
echo "-----------------------------------------------"
chmod +x bin/console
echo "✔️ bin/console est exécutable."

echo ""
echo "🧹 3. Nettoyage du cache..."
echo "-----------------------------------------------"
rm -rf var/cache/*
echo "✔️ Cache supprimé."

echo ""
echo "🔥 4. Reconstruction du cache Symfony..."
echo "-----------------------------------------------"
bin/console cache:clear --env=dev
bin/console cache:warmup --env=dev
echo "✔️ Cache reconstruit."

echo ""
echo "🚀 5. Redémarrage du serveur Symfony..."
echo "-----------------------------------------------"
symfony server:start -d --no-tls --port=8000
echo "✔️ Serveur relancé."

echo ""
echo "🌍 6. URL du serveur local"
echo "-----------------------------------------------"
echo "➡️  http://127.0.0.1:8000"

echo ""
echo "==============================================="
echo "      REDÉMARRAGE SYMFONY TERMINÉ"
echo "==============================================="
