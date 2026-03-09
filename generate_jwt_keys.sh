#!/bin/bash

echo "==============================================="
echo "      GÉNÉRATION AUTOMATIQUE DES CLÉS JWT"
echo "==============================================="

JWT_DIR="config/jwt"

echo ""
echo "📁 1. Création du dossier JWT si nécessaire..."
echo "-----------------------------------------------"
mkdir -p $JWT_DIR

echo ""
echo "🔐 2. Génération de la clé privée..."
echo "-----------------------------------------------"
openssl genpkey -algorithm RSA -out $JWT_DIR/private.pem -pkeyopt rsa_keygen_bits:4096

echo ""
echo "🔓 3. Génération de la clé publique..."
echo "-----------------------------------------------"
openssl pkey -in $JWT_DIR/private.pem -pubout -out $JWT_DIR/public.pem

echo ""
echo "📝 4. Mise à jour du fichier .env.local..."
echo "-----------------------------------------------"

if ! grep -q "JWT_SECRET_KEY" .env.local 2>/dev/null; then
    echo "JWT_SECRET_KEY=$JWT_DIR/private.pem" >> .env.local
    echo "JWT_PUBLIC_KEY=$JWT_DIR/public.pem" >> .env.local
    echo "JWT_PASSPHRASE=" >> .env.local
    echo "✔️ Variables JWT ajoutées dans .env.local"
else
    echo "✔️ Variables JWT déjà présentes dans .env.local"
fi

echo ""
echo "📝 5. Mise à jour du fichier .env (si nécessaire)..."
echo "-----------------------------------------------"

if ! grep -q "JWT_SECRET_KEY" .env 2>/dev/null; then
    echo "" >> .env
    echo "###> lexik/jwt-authentication-bundle ###" >> .env
    echo "JWT_SECRET_KEY=$JWT_DIR/private.pem" >> .env
    echo "JWT_PUBLIC_KEY=$JWT_DIR/public.pem" >> .env
    echo "JWT_PASSPHRASE=" >> .env
    echo "###< lexik/jwt-authentication-bundle ###" >> .env
    echo "✔️ Variables JWT ajoutées dans .env"
else
    echo "✔️ Variables JWT déjà présentes dans .env"
fi

echo ""
echo "==============================================="
echo "      CLÉS JWT GÉNÉRÉES ET CONFIGURÉES"
echo "==============================================="
echo "➡️  Private key : $JWT_DIR/private.pem"
echo "➡️  Public key  : $JWT_DIR/public.pem"
echo "==============================================="
