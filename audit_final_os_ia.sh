#!/bin/bash

echo "==============================================="
echo "      AUDIT FINAL — MIDDO OS + IA"
echo "==============================================="

echo ""
echo "🔍 1. Recherche des anciens chemins Twig..."
echo "-----------------------------------------------"

OLD_PATHS=(
    "dashboard_os/"
    "layout/"
    "menu/"
    "cockpit/"
    "workspace/"
    "notification/"
    "settings/"
    "ia/"
    "ia_dashboard/"
    "ia_insights/"
    "forecast_ai/"
    "trends_ai/"
    "risk_ai/"
    "risk_analyzer/"
    "pricing_ai/"
    "marketplace_ai/"
    "matching/"
    "purchase_ai/"
    "purchase_order/"
    "purchase_request/"
    "purchase_transaction/"
    "supplier_offer/"
    "order/"
    "client_order/"
    "pricing/"
    "bank/"
    "escrow/"
    "transactions/"
    "transaction/"
    "finances/"
    "transport/"
    "transport_dashboard/"
    "delivery/"
    "delivery_assignment/"
    "delivery_segment/"
    "delivery_tracking/"
    "logistic_contract/"
    "project_builder/"
    "project_manager/"
    "projets/"
)

for PATH in "${OLD_PATHS[@]}"; do
    MATCHES=$(grep -Rsl "$PATH" .)
    if [ ! -z "$MATCHES" ]; then
        echo "⚠️  Chemin restant détecté : $PATH"
        echo "$MATCHES"
        echo ""
    fi
done

echo ""
echo "🔍 2. Recherche des anciens dossiers encore présents..."
echo "-----------------------------------------------"

for DIR in "${OLD_PATHS[@]}"; do
    if [ -d "templates/$DIR" ]; then
        echo "⚠️  Ancien dossier trouvé : templates/$DIR"
    fi
done

echo ""
echo "🔍 3. Recherche des appels render() non migrés..."
echo "-----------------------------------------------"

grep -Rsn "render(" src/ | grep -E "dashboard_os|ia_|purchase_|order|transport_|delivery_|risk_|pricing_|matching" || echo "✔️ Aucun appel render() problématique détecté."

echo ""
echo "🔍 4. Recherche des fichiers Twig vides..."
echo "-----------------------------------------------"

find . -name "*.twig" -size 0 -print

echo ""
echo "🔍 5. Recherche des dossiers vides..."
echo "-----------------------------------------------"

find . -type d -empty -print

echo ""
echo "==============================================="
echo "        AUDIT FINAL TERMINÉ"
echo "==============================================="
