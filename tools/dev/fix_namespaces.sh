#!/bin/bash

echo "🔧 Correction automatique des namespaces (version améliorée)..."

find src/Controller -name "*.php" -print0 | while IFS= read -r -d '' file; do
  namespace="App\\Controller"

  if [[ "$file" == *"/Api/"* ]]; then
    namespace="App\\Controller\\Api"
  elif [[ "$file" == *"/Admin/"* ]]; then
    namespace="App\\Controller\\Admin"
  elif [[ "$file" == *"/System/"* ]]; then
    namespace="App\\Controller\\System"
  elif [[ "$file" == *"/OS/"* ]]; then
    namespace="App\\Controller\\OS"
  elif [[ "$file" == *"/User/"* ]]; then
    namespace="App\\Controller\\User"
  elif [[ "$file" == *"/Payment/"* ]]; then
    namespace="App\\Controller\\Payment"
  elif [[ "$file" == *"/Project/"* ]]; then
    namespace="App\\Controller\\Project"
  elif [[ "$file" == *"/Notification/"* ]]; then
    namespace="App\\Controller\\Notification"
  elif [[ "$file" == *"/Ia/"* ]]; then
    namespace="App\\Controller\\Ia"
  fi

  # Remplace n'importe quelle ligne contenant "namespace"
  sed -i "s/^namespace .*/namespace $namespace;/" "$file"
  sed -i "s/^namespace.*/namespace $namespace;/" "$file"
  sed -i "s/namespace App.*/namespace $namespace;/" "$file"

  echo "✔️ Namespace corrigé : $file → $namespace"
done

echo "🎉 Correction terminée."
