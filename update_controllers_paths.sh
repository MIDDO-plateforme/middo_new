#!/bin/bash

echo "=== MISE À JOUR AUTOMATIQUE DES CHEMINS TWIG DANS LES CONTRÔLEURS ==="

declare -A MAP=(
    ["dashboard_os/"]="core_os/dashboard/"
    ["layout/"]="core_os/layout/"
    ["menu/"]="core_os/navigation/"
    ["cockpit/"]="core_os/cockpit/"
    ["workspace/"]="core_os/workspace/"
    ["notification/"]="core_os/notifications/"
    ["settings/"]="core_os/settings/"

    ["ia/"]="ia_engine/assistants/"
    ["ia_dashboard/"]="ia_engine/insights/"
    ["ia_insights/"]="ia_engine/insights/"
    ["forecast_ai/"]="ia_engine/predictions/"
    ["trends_ai/"]="ia_engine/insights/"
    ["risk_ai/"]="ia_engine/risk/"
    ["risk_analyzer/"]="ia_engine/risk/"
    ["pricing_ai/"]="ia_engine/pricing/"
    ["marketplace_ai/"]="ia_engine/marketplace/"
    ["matching/"]="ia_engine/matching/"
    ["purchase_ai/"]="ia_engine/automation/"

    ["purchase_order/"]="domains/commerce/purchase/"
    ["purchase_request/"]="domains/commerce/purchase/"
    ["purchase_transaction/"]="domains/commerce/purchase/"
    ["supplier_offer/"]="domains/commerce/supplier/"
    ["order/"]="domains/commerce/orders/"
    ["client_order/"]="domains/commerce/orders/"
    ["pricing/"]="domains/commerce/pricing/"

    ["bank/"]="domains/finance/bank/"
    ["escrow/"]="domains/finance/escrow/"
    ["transactions/"]="domains/finance/transactions/"
    ["transaction/"]="domains/finance/transactions/"
    ["finances/"]="domains/finance/"

    ["transport/"]="domains/logistics/transport/"
    ["transport_dashboard/"]="domains/logistics/transport/"
    ["delivery/"]="domains/logistics/delivery/"
    ["delivery_assignment/"]="domains/logistics/delivery/"
    ["delivery_segment/"]="domains/logistics/delivery/"
    ["delivery_tracking/"]="domains/logistics/delivery/"
    ["logistic_contract/"]="domains/logistics/contracts/"

    ["project_builder/"]="domains/projects/builder/"
    ["project_manager/"]="domains/projects/manager/"
    ["projets/"]="domains/projects/"
)

for OLD in "${!MAP[@]}"; do
    NEW=${MAP[$OLD]}
    echo "→ Mise à jour contrôleurs : $OLD  →  $NEW"
    grep -rl "$OLD" src/ | xargs sed -i "s|$OLD|$NEW|g" 2>/dev/null
done

echo "=== MISE À JOUR DES CONTRÔLEURS TERMINÉE ==="
