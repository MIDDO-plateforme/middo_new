# ============================================
# MIDDO – Makefile Universel (Version Ultime)
# ============================================

# --- Chemins absolus ---
MIDDO_CLEAN := C:/Users/MBANE LOKOTA/middo_clean
MIDDO_NEW   := C:/Users/MBANE LOKOTA/middo_new

# --- Détection de l’OS ---
ifeq ($(OS),Windows_NT)
    DETECTED_OS := windows
else
    DETECTED_OS := unix
endif

# --- Commandes selon OS ---
ifeq ($(DETECTED_OS),windows)
    SYMFONY := symfony.exe
    STOP_SERVER := -$(SYMFONY) local:server:stop --all >NUL 2>&1
    START_SERVER := $(SYMFONY) local:server:start --no-tls --quiet >NUL 2>&1
    SLEEP := timeout /t 2 >NUL
    OPEN_BROWSER := start "" http://127.0.0.1:8000/healthz/full
    LOGFILE_PATH := C:\Users\MBANE LOKOTA\.symfony5\log
else
    SYMFONY := symfony
    STOP_SERVER := -$(SYMFONY) local:server:stop --all >/dev/null 2>&1
    START_SERVER := $(SYMFONY) local:server:start --no-tls --quiet >/dev/null 2>&1
    SLEEP := sleep 2
    OPEN_BROWSER := xdg-open http://127.0.0.1:8000/healthz/full || open http://127.0.0.1:8000/healthz/full
    LOGFILE_PATH := ~/.symfony5/log
endif

# --- Logging ---
LOGFILE := middo.log
define log
    @echo [MIDDO] $(1)
endef

# --- Règles principales ---
.PHONY: clean new stop start restart logs status doctor health \
        cache-clear migrate fixtures composer-update composer-install phpinfo \
        deploy test seed backup restore docker-up docker-down env-check \
        qa swagger queue

clean: stop
    $(call log,"Nettoyage du projet…")
    @cd "$(MIDDO_CLEAN)" && git rev-parse --is-inside-work-tree >NUL 2>&1 && git reset --hard >NUL 2>&1 || echo "Pas un repo Git"
    @cd "$(MIDDO_CLEAN)" && git rev-parse --is-inside-work-tree >NUL 2>&1 && git clean -fd >NUL 2>&1 || echo "Rien à nettoyer"
    $(call log,"Nettoyage terminé.")

new: stop
    $(call log,"Création d’un nouveau projet…")
    @cd "$(MIDDO_NEW)" && composer install >NUL 2>&1
    $(call log,"Nouveau projet prêt.")

stop:
    $(call log,"Arrêt du serveur Symfony…")
    $(STOP_SERVER)
    $(call log,"Serveur arrêté (ou déjà arrêté).")

start:
    $(call log,"Démarrage du serveur Symfony…")
    $(START_SERVER)
    $(SLEEP)
    $(OPEN_BROWSER)
    $(call log,"Serveur démarré.")

restart: stop
    $(call log,"Redémarrage du serveur Symfony…")
    $(START_SERVER)
    $(SLEEP)
    $(OPEN_BROWSER)
    $(call log,"Serveur redémarré.")

logs:
    $(call log,"Affichage des logs Symfony…")
    @powershell -Command "Get-ChildItem '$(LOGFILE_PATH)' -Recurse | Sort-Object LastWriteTime -Descending | Select-Object -First 1 | Get-Content -Wait"

status:
    $(call log,"Statut du serveur Symfony…")
    $(SYMFONY) local:server:status

doctor:
    $(call log,"Diagnostic complet du projet…")
    $(SYMFONY) local:server:status
    $(SYMFONY) check:requirements || true
    php -v
    composer --version
    git --version
    $(call log,"Diagnostic terminé.")

health:
    $(call log,"Vérification de l’état de l’API…")
    $(OPEN_BROWSER)
    $(call log,"Page healthz ouverte.")

cache-clear:
    $(call log,"Vidage du cache Symfony…")
    php bin/console cache:clear --no-warmup
    $(call log,"Cache vidé.")

migrate:
    $(call log,"Exécution des migrations…")
    php bin/console doctrine:migrations:migrate --no-interaction
    $(call log,"Migrations terminées.")

fixtures:
    $(call log,"Chargement des fixtures…")
    php bin/console doctrine:fixtures:load --no-interaction
    $(call log,"Fixtures chargées.")

composer-update:
    $(call log,"Mise à jour des dépendances Composer…")
    composer update
    $(call log,"Dépendances mises à jour.")

composer-install:
    $(call log,"Installation des dépendances Composer…")
    composer install
    $(call log,"Dépendances installées.")

phpinfo:
    $(call log,"Informations PHP…")
    php -i

deploy:
    $(call log,"Déploiement du projet…")
    git pull
    composer install --no-dev --optimize-autoloader
    php bin/console cache:clear --no-warmup
    php bin/console cache:warmup
    $(call log,"Déploiement terminé.")

test:
    $(call log,"Exécution des tests…")
    php bin/phpunit
    $(call log,"Tests terminés.")

seed:
    $(call log,"Chargement des données de démonstration…")
    php bin/console doctrine:fixtures:load --no-interaction
    $(call log,"Données chargées.")

backup:
    $(call log,"Sauvegarde de la base de données…")
    php bin/console doctrine:database:dump --force
    $(call log,"Sauvegarde terminée.")

restore:
    $(call log,"Restauration de la base de données…")
    php bin/console doctrine:database:import backup.sql
    $(call log,"Restauration terminée.")

docker-up:
    $(call log,"Démarrage de Docker…")
    docker compose up -d
    $(call log,"Docker démarré.")

docker-down:
    $(call log,"Arrêt de Docker…")
    docker compose down
    $(call log,"Docker arrêté.")

env-check:
    $(call log,"Vérification des variables d’environnement…")
    php bin/console debug:container --env-vars
    $(call log,"Vérification terminée.")

qa:
    $(call log,"Qualité du code (QA)…")
    php vendor/bin/phpstan analyse || true
    php vendor/bin/php-cs-fixer fix --dry-run || true
    $(call log,"QA terminée (voir détails ci-dessus).")

swagger:
    $(call log,"Génération / mise à jour de la documentation API (Swagger/OpenAPI)…")
    php bin/console api:openapi:export --output=var/openapi.json || true
    $(call log,"Documentation API générée (var/openapi.json).")

queue:
    $(call log,"Démarrage du consommateur de messages (Messenger)…")
    php bin/console messenger:consume async -vv || true
    $(call log,"Consommateur arrêté.")
