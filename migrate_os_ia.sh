#!/bin/bash

echo "=== MIGRATION MIDDO OS + IA ==="

###############################################
# 1. CORE_OS
###############################################

mkdir -p core_os/layout
mv templates/layout/* core_os/layout/ 2>/dev/null

mkdir -p core_os/navigation
mv templates/core_os/navigation/* core_os/navigation/ 2>/dev/null

mkdir -p core_os/dashboard
mv templates/core_os/dashboard/* core_os/dashboard/ 2>/dev/null

mkdir -p core_os/cockpit
mv templates/core_os/cockpit/* core_os/core_os/cockpit/ 2>/dev/null

mkdir -p core_os/workspace
mv templates/core_os/workspace/* core_os/core_os/workspace/ 2>/dev/null

mkdir -p core_os/notifications
mv templates/core_os/notifications/* core_os/notifications/ 2>/dev/null

mkdir -p core_os/settings
mv templates/core_os/settings/* core_os/core_os/settings/ 2>/dev/null


###############################################
# 2. IA_ENGINE
###############################################

mkdir -p ia_engine/assistants
mv templates/ia_engine/assistants/* ia_engine/assistants/ 2>/dev/null

mkdir -p ia_engine/insights
mv templates/ia_engine/insights/* ia_engine/insights/ 2>/dev/null
mv templates/ia_engine/insights/* ia_engine/insights/ 2>/dev/null
mv templates/ia_engine/insights/* ia_engine/insights/ 2>/dev/null

mkdir -p ia_engine/predictions
mv templates/ia_engine/predictions/* ia_engine/predictions/ 2>/dev/null

mkdir -p ia_engine/risk
mv templates/ia_engine/risk/* ia_engine/risk/ 2>/dev/null
mv templates/ia_engine/risk/* ia_engine/risk/ 2>/dev/null

mkdir -p ia_engine/pricing
mv templates/ia_engine/pricing/* ia_engine/domains/commerce/pricing/ 2>/dev/null

mkdir -p ia_engine/marketplace
mv templates/ia_engine/marketplace/* ia_engine/marketplace/ 2>/dev/null

mkdir -p ia_engine/matching
mv templates/ia_engine/matching/* ia_engine/ia_engine/matching/ 2>/dev/null

mkdir -p ia_engine/automation
mv templates/ia_engine/automation/* ia_engine/automation/ 2>/dev/null


###############################################
# 3. DOMAINS
###############################################

# PEOPLE
mkdir -p domains/people/user
mv templates/user/* domains/people/user/ 2>/dev/null

mkdir -p domains/people/freelancer
mv templates/freelancer/* domains/people/freelancer/ 2>/dev/null

mkdir -p domains/people/entrepreneur
mv templates/entrepreneur/* domains/people/entrepreneur/ 2>/dev/null

mkdir -p domains/people/investor
mv templates/investor/* domains/people/investor/ 2>/dev/null

mkdir -p domains/people/company
mv templates/company/* domains/people/company/ 2>/dev/null


# COMMERCE
mkdir -p domains/commerce/purchase
mv templates/domains/commerce/purchase/* domains/commerce/purchase/ 2>/dev/null
mv templates/domains/commerce/purchase/* domains/commerce/purchase/ 2>/dev/null
mv templates/domains/commerce/purchase/* domains/commerce/purchase/ 2>/dev/null

mkdir -p domains/commerce/supplier
mv templates/domains/commerce/supplier/* domains/commerce/supplier/ 2>/dev/null

mkdir -p domains/commerce/orders
mv templates/domains/commerce/orders/* domains/commerce/orders/ 2>/dev/null
mv templates/domains/commerce/orders/* domains/commerce/orders/ 2>/dev/null

mkdir -p domains/commerce/pricing
mv templates/domains/commerce/pricing/* domains/commerce/domains/commerce/pricing/ 2>/dev/null


# FINANCE
mkdir -p domains/finance/bank
mv templates/domains/finance/bank/* domains/finance/domains/finance/bank/ 2>/dev/null

mkdir -p domains/finance/escrow
mv templates/domains/finance/escrow/* domains/finance/domains/finance/escrow/ 2>/dev/null

mkdir -p domains/finance/transactions
mv templates/domains/finance/transactions/* domains/finance/domains/finance/transactions/ 2>/dev/null
mv templates/domains/finance/transactions/* domains/finance/domains/finance/transactions/ 2>/dev/null

mv templates/domains/finance/* domains/finance/ 2>/dev/null


# LOGISTICS
mkdir -p domains/logistics/transport
mv templates/domains/logistics/transport/* domains/logistics/domains/logistics/transport/ 2>/dev/null
mv templates/domains/logistics/domains/logistics/transport/* domains/logistics/domains/logistics/transport/ 2>/dev/null

mkdir -p domains/logistics/delivery
mv templates/domains/logistics/delivery/* domains/logistics/domains/logistics/delivery/ 2>/dev/null
mv templates/domains/logistics/domains/logistics/delivery/* domains/logistics/domains/logistics/delivery/ 2>/dev/null
mv templates/domains/logistics/delivery/* domains/logistics/domains/logistics/delivery/ 2>/dev/null
mv templates/domains/logistics/delivery/* domains/logistics/domains/logistics/delivery/ 2>/dev/null

mkdir -p domains/logistics/contracts
mv templates/domains/logistics/contracts/* domains/logistics/contracts/ 2>/dev/null


# PROJECTS
mkdir -p domains/projects/builder
mv templates/domains/projects/builder/* domains/projects/builder/ 2>/dev/null

mkdir -p domains/projects/manager
mv templates/domains/projects/manager/* domains/projects/manager/ 2>/dev/null

mkdir -p domains/projects
mv templates/domains/projects/* domains/projects/ 2>/dev/null

mkdir -p domains/projects/workspace
mv templates/core_os/workspace/* domains/projects/core_os/workspace/ 2>/dev/null


###############################################
# 4. COMPONENTS
###############################################

mkdir -p components/cards
mv templates/components/cards/* components/cards/ 2>/dev/null

mkdir -p components/tables
mv templates/components/tables/* components/tables/ 2>/dev/null

mkdir -p components/forms
mv templates/components/forms/* components/forms/ 2>/dev/null

mkdir -p components/modals
mv templates/components/modals/* components/modals/ 2>/dev/null

mkdir -p components/charts
mv templates/components/charts/* components/charts/ 2>/dev/null

mkdir -p components/widgets
mv templates/components/widgets/* components/widgets/ 2>/dev/null


###############################################
# 5. LEGACY
###############################################

mkdir -p legacy/dist
mv dist/* legacy/dist/ 2>/dev/null

mkdir -p legacy/archives
mv archives/* legacy/archives/ 2>/dev/null

mkdir -p legacy/templates_backup
mv templates_backup_* legacy/templates_backup/ 2>/dev/null

mkdir -p legacy/old_scripts
mv dev_scripts/* legacy/old_scripts/ 2>/dev/null


###############################################
# 6. SYSTEM
###############################################

mkdir -p system/config
mv config/* system/config/ 2>/dev/null

mkdir -p system/migrations
mv migrations/* system/migrations/ 2>/dev/null

mkdir -p system/tests
mv tests/* system/tests/ 2>/dev/null

mkdir -p system/tools
mv tools/* system/tools/ 2>/dev/null

mkdir -p system/scripts
mv private/scripts/* system/scripts/ 2>/dev/null


echo "=== MIGRATION TERMINÉE ==="
