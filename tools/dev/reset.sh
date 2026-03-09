#!/bin/bash

echo "🛑 Fermeture des serveurs Symfony CLI..."
pkill -f "symfony"

echo "🔍 Libération du port 8000..."
PID=$(lsof -t -i:8000)
if [ ! -z "$PID" ]; then
  echo "⚠️ Processus trouvé sur 8000 (PID=$PID), suppression..."
  kill -9 $PID
fi

echo "🧹 Nettoyage du cache Symfony..."
rm -rf var/cache/*

echo "🔎 Vérification des namespaces des contrôleurs..."
find src/Controller -name "*.php" -print0 | while IFS= read -r -d '' file; do
  expected="namespace App\\Controller"
  [[ "$file" == *"/Api/"* ]] && expected="namespace App\\Controller\\Api"
  if ! grep -q "$expected" "$file"; then
    echo "⚠️ Namespace incorrect : $file"
  fi
done

echo "🚀 Lancement du serveur PHP..."
php -S 127.0.0.1:8000 -t public
