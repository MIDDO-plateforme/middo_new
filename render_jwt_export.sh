#!/bin/bash

echo "==============================================="
echo "      EXPORT JWT POUR RENDER — MIDDO OS + IA"
echo "==============================================="

JWT_DIR="config/jwt"
PRIVATE_KEY="$JWT_DIR/private.pem"
PUBLIC_KEY="$JWT_DIR/public.pem"

echo ""
echo "🔍 1. Vérification des clés JWT..."
echo "-----------------------------------------------"

if [ ! -f "$PRIVATE_KEY" ]; then
    echo "❌ Clé privée manquante : $PRIVATE_KEY"
    echo "➡️  Lance d'abord : ./generate_jwt_keys.sh"
    exit 1
fi

if [ ! -f "$PUBLIC_KEY" ]; then
    echo "❌ Clé publique manquante : $PUBLIC_KEY"
    echo "➡️  Lance d'abord : ./generate_jwt_keys.sh"
    exit 1
fi

echo "✔️ Clés JWT trouvées."

echo ""
echo "📝 2. Génération du fichier render_env.txt..."
echo "-----------------------------------------------"

cat <<EOF > render_env.txt
# Variables JWT pour Render
JWT_SECRET_KEY=$PRIVATE_KEY
JWT_PUBLIC_KEY=$PUBLIC_KEY
JWT_PASSPHRASE=
EOF

echo "✔️ Fichier généré : render_env.txt"

echo ""
echo "📤 3. Instructions pour Render"
echo "-----------------------------------------------"
echo "➡️ Ouvre Render Dashboard"
echo "➡️ Va dans : Environment → Environment Variables"
echo "➡️ Ajoute les variables suivantes :"
echo ""
echo "JWT_SECRET_KEY=$PRIVATE_KEY"
echo "JWT_PUBLIC_KEY=$PUBLIC_KEY"
echo "JWT_PASSPHRASE="
echo ""
echo "⚠️ IMPORTANT :"
echo "Render ne doit jamais recevoir le contenu de la clé privée."
echo "Il doit seulement recevoir le chemin du fichier."
echo ""
echo "==============================================="
echo "      EXPORT JWT POUR RENDER TERMINÉ"
echo "==============================================="
