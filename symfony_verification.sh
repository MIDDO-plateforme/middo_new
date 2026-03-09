#!/bin/bash

echo "==============================================="
echo "      VÉRIFICATION SYMFONY — MIDDO OS + IA"
echo "==============================================="

echo ""
echo "🔍 1. Vérification des templates Twig..."
echo "-----------------------------------------------"
bin/console lint:twig templates/ || echo "⚠️ Erreurs Twig détectées."

echo ""
echo "🔍 2. Vérification des fichiers YAML..."
echo "-----------------------------------------------"
bin/console lint:yaml config/ || echo "⚠️ Erreurs YAML détectées."

echo ""
echo "🔍 3. Vérification du container Symfony..."
echo "-----------------------------------------------"
bin/console lint:container || echo "⚠️ Erreurs dans les services Symfony."

echo ""
echo "🔍 4. Vérification des routes..."
echo "-----------------------------------------------"
bin/console lint:routes || echo "⚠️ Erreurs dans les routes Symfony."

echo ""
echo "🔍 5. Vérification de composer.json..."
echo "-----------------------------------------------"
composer validate || echo "⚠️ composer.json contient des erreurs."

echo ""
echo "🔍 6. Vérification syntaxique PHP..."
echo "-----------------------------------------------"
find src -name "*.php" -print0 | xargs -0 -n1 php -l | grep -v "No syntax errors" || echo "⚠️ Erreurs PHP détectées."

echo ""
echo "🔍 7. Analyse des logs Symfony..."
echo "-----------------------------------------------"
if [ -d "var/log" ]; then
    grep -R "CRITICAL\|ERROR" var/log/ || echo "✔️ Aucun message d'erreur critique dans les logs."
else
    echo "⚠️ Aucun dossier var/log trouvé."
fi

echo ""
echo "==============================================="
echo "      VÉRIFICATION SYMFONY TERMINÉE"
echo "==============================================="
