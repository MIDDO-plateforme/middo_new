#!/bin/bash

echo "==============================================="
echo "      TEST COMPLET JWT — MIDDO OS + IA"
echo "==============================================="

PRIVATE_KEY="config/jwt/private.pem"
PUBLIC_KEY="config/jwt/public.pem"

echo ""
echo "🔍 1. Vérification des clés JWT..."
echo "-----------------------------------------------"

if [ ! -f "$PRIVATE_KEY" ]; then
    echo "❌ Clé privée manquante : $PRIVATE_KEY"
    exit 1
fi

if [ ! -f "$PUBLIC_KEY" ]; then
    echo "❌ Clé publique manquante : $PUBLIC_KEY"
    exit 1
fi

echo "✔️ Clés JWT trouvées."

echo ""
echo "🔧 2. Vérification de la configuration Symfony..."
echo "-----------------------------------------------"

if ! grep -q "JWT_SECRET_KEY" .env.local; then
    echo "❌ JWT_SECRET_KEY manquant dans .env.local"
    exit 1
fi

echo "✔️ Configuration JWT trouvée dans .env.local."

echo ""
echo "🛠️ 3. Génération d'un token JWT de test..."
echo "-----------------------------------------------"

TOKEN=$(bin/console lexik:jwt:encode '{"username": "test-user"}' 2>/dev/null)

if [ -z "$TOKEN" ]; then
    echo "❌ Impossible de générer un token JWT."
    echo "➡️ Vérifie que le bundle LexikJWT est installé."
    exit 1
fi

echo "✔️ Token généré :"
echo "$TOKEN"

echo ""
echo "🔎 4. Décodage du token JWT..."
echo "-----------------------------------------------"

DECODED=$(bin/console lexik:jwt:decode "$TOKEN" 2>/dev/null)

if [ -z "$DECODED" ]; then
    echo "❌ Impossible de décoder le token JWT."
    exit 1
fi

echo "✔️ Token décodé :"
echo "$DECODED"

echo ""
echo "==============================================="
echo "      TEST JWT TERMINÉ — TOUT EST FONCTIONNEL"
echo "==============================================="
