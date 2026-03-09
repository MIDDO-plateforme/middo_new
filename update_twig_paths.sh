#!/bin/bash

echo "=== MISE À JOUR AUTOMATIQUE DES CHEMINS TWIG ==="

declare -A MAP=(
    ["core_os/dashboard/"]="core_os/dashboard/"
    ["layout/"]="core_os/layout/"
    ["core_os/navigation/"]="core_os/navigation/"
    ["core_os/cockpit/"]="core_os/core_os/cockpit/"
    ["core_os/workspace/"]="core_os/core_os/workspace/"
    ["core_os/notifications/"]="core_os/notifications/"
    ["core_os/settings/"]="core_os/core_os/settings/"

    ["ia_engine/assistants/"]="ia_engine/assistants/"
    ["ia_engine/insights/"]="ia_engine/insights/"
    ["ia_engine/insights/"]="ia_engine/insights/"
    ["ia_engine/predictions/"]="ia_engine/predictions/"
    ["ia_engine/insights/"]="ia_engine/insights/"
    ["ia_engine/risk/"]="ia_engine/risk/"
    ["ia_engine/risk/"]="ia_engine/risk/"
    ["ia_engine/pricing/"]="ia_engine/domains/commerce/pricing/"
    ["ia_engine/marketplace/"]="ia_engine/marketplace/"
    ["ia_engine/matching/"]="ia_engine/ia_engine/matching/"
    ["ia_engine/automation/"]="ia_engine/automation/"

    ["domains/commerce/purchase/"]="domains/commerce/purchase/"
    ["domains/commerce/purchase/"]="domains/commerce/purchase/"
    ["domains/commerce/purchase/"]="domains/commerce/purchase/"
    ["domains/commerce/supplier/"]="domains/commerce/supplier/"
    ["domains/commerce/orders/"]="domains/commerce/orders/"
    ["domains/commerce/orders/"]="domains/commerce/orders/"
    ["domains/commerce/pricing/"]="domains/commerce/domains/commerce/pricing/"

    ["domains/finance/bank/"]="domains/finance/domains/finance/bank/"
    ["domains/finance/escrow/"]="domains/finance/domains/finance/escrow/"
    ["domains/finance/transactions/"]="domains/finance/domains/finance/transactions/"
    ["domains/finance/transactions/"]="domains/finance/domains/finance/transactions/"
    ["domains/finance/"]="domains/finance/"

    ["domains/logistics/transport/"]="domains/logistics/domains/logistics/transport/"
    ["domains/logistics/domains/logistics/transport/"]="domains/logistics/domains/logistics/transport/"
    ["domains/logistics/delivery/"]="domains/logistics/domains/logistics/delivery/"
    ["domains/logistics/domains/logistics/delivery/"]="domains/logistics/domains/logistics/delivery/"
    ["domains/logistics/delivery/"]="domains/logistics/domains/logistics/delivery/"
    ["domains/logistics/delivery/"]="domains/logistics/domains/logistics/delivery/"
    ["domains/logistics/contracts/"]="domains/logistics/contracts/"

    ["domains/projects/builder/"]="domains/projects/builder/"
    ["domains/projects/manager/"]="domains/projects/manager/"
    ["domains/projects/"]="domains/projects/"
)

for OLD in "${!MAP[@]}"; do
    NEW=${MAP[$OLD]}
    echo "→ Remplacement : $OLD  →  $NEW"
    grep -rl "$OLD" . | xargs sed -i "s|$OLD|$NEW|g" 2>/dev/null
done

echo "=== MISE À JOUR TERMINÉE ==="
