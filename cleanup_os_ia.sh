#!/bin/bash

echo "==============================================="
echo "      NETTOYAGE FINAL — MIDDO OS + IA"
echo "==============================================="

echo ""
echo "🧹 1. Suppression des anciens dossiers templates..."
echo "-----------------------------------------------"

OLD_DIRS=(
    "templates/dashboard_os"
    "templates/layout"
    "templates/menu"
    "templates/cockpit"
    "templates/workspace"
    "templates/notification"
    "templates/settings"
    "templates/ia"
    "templates/ia_dashboard"
    "templates/ia_insights"
    "templates/forecast_ai"
    "templates/trends_ai"
    "templates/risk_ai"
    "templates/risk_analyzer"
    "templates/pricing_ai"
    "templates/marketplace_ai"
    "templates/matching"
    "templates/purchase_ai"
    "templates/purchase_order"
    "templates/purchase_request"
    "templates/purchase_transaction"
    "templates/supplier_offer"
    "templates/order"
    "templates/client_order"
    "templates/bank"
    "templates/escrow"
    "templates/transactions"
    "templates/transaction"
    "templates/finances"
    "templates/transport"
    "templates/transport_dashboard"
    "templates/delivery"
    "templates/delivery_assignment"
    "templates/delivery_segment"
    "templates/delivery_tracking"
    "templates/logistic_contract"
    "templates/project_builder"
    "templates/project_manager"
    "templates/projets"
)

for DIR in "${OLD_DIRS[@]}"; do
    if [ -d "$DIR" ]; then
        echo "🗑️ Suppression : $DIR"
        rm -rf "$DIR"
    fi
done

echo ""
echo "🧹 2. Suppression des fichiers Twig vides..."
echo "-----------------------------------------------"

find . -name "*.twig" -size 0 -print -delete

echo ""
echo "🧹 3. Suppression des dossiers vides..."
echo "-----------------------------------------------"

find . -type d -empty -print -delete

echo ""
echo "==============================================="
echo "      NETTOYAGE FINAL TERMINÉ"
echo "==============================================="
