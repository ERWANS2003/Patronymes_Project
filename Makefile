# Makefile pour l'application Patronymes Docker
# Usage: make [target]

.PHONY: help build start stop restart logs clean migrate seed shell test

# Variables
COMPOSE = docker-compose
APP_CONTAINER = app
DB_CONTAINER = db

# Aide par défaut
help: ## Afficher cette aide
	@echo "Commandes disponibles :"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

build: ## Construire l'image Docker
	$(COMPOSE) build --no-cache

start: ## Démarrer tous les services
	$(COMPOSE) up -d
	@echo "Attente de la base de données..."
	@sleep 10
	@make migrate

stop: ## Arrêter tous les services
	$(COMPOSE) down

restart: ## Redémarrer tous les services
	$(COMPOSE) restart

logs: ## Afficher les logs en temps réel
	$(COMPOSE) logs -f

logs-app: ## Afficher les logs de l'application
	$(COMPOSE) logs -f $(APP_CONTAINER)

logs-db: ## Afficher les logs de la base de données
	$(COMPOSE) logs -f $(DB_CONTAINER)

migrate: ## Exécuter les migrations
	$(COMPOSE) exec $(APP_CONTAINER) php artisan migrate --force

seed: ## Exécuter les seeders
	$(COMPOSE) exec $(APP_CONTAINER) php artisan db:seed --force

fresh: ## Recréer la base de données et exécuter les migrations
	$(COMPOSE) exec $(APP_CONTAINER) php artisan migrate:fresh --force

shell: ## Accéder au shell du conteneur application
	$(COMPOSE) exec $(APP_CONTAINER) bash

shell-db: ## Accéder au shell de la base de données
	$(COMPOSE) exec $(DB_CONTAINER) psql -U patronymes -d patronymes

cache-clear: ## Vider le cache de l'application
	$(COMPOSE) exec $(APP_CONTAINER) php artisan cache:clear
	$(COMPOSE) exec $(APP_CONTAINER) php artisan config:clear
	$(COMPOSE) exec $(APP_CONTAINER) php artisan route:clear
	$(COMPOSE) exec $(APP_CONTAINER) php artisan view:clear

optimize: ## Optimiser l'application
	$(COMPOSE) exec $(APP_CONTAINER) php artisan config:cache
	$(COMPOSE) exec $(APP_CONTAINER) php artisan route:cache
	$(COMPOSE) exec $(APP_CONTAINER) php artisan view:cache

test: ## Exécuter les tests
	$(COMPOSE) exec $(APP_CONTAINER) php artisan test

status: ## Afficher le statut des conteneurs
	$(COMPOSE) ps

clean: ## Nettoyer les ressources Docker
	$(COMPOSE) down -v --rmi all --remove-orphans
	docker system prune -f

backup: ## Sauvegarder la base de données
	$(COMPOSE) exec $(DB_CONTAINER) pg_dump -U patronymes patronymes > backup_$(shell date +%Y%m%d_%H%M%S).sql

restore: ## Restaurer la base de données (usage: make restore FILE=backup.sql)
	@if [ -z "$(FILE)" ]; then echo "Usage: make restore FILE=backup.sql"; exit 1; fi
	$(COMPOSE) exec -T $(DB_CONTAINER) psql -U patronymes patronymes < $(FILE)

install: ## Installation complète (build + start + migrate + seed)
	make build
	make start
	make seed

update: ## Mise à jour de l'application
	git pull
	make build
	make migrate

# Commandes de développement
dev: ## Démarrer en mode développement avec logs
	$(COMPOSE) up

dev-build: ## Construire et démarrer en mode développement
	$(COMPOSE) up --build

# Commandes de production
prod: ## Configuration pour la production
	@echo "Configuration pour la production..."
	@echo "N'oubliez pas de :"
	@echo "1. Changer les mots de passe par défaut"
	@echo "2. Configurer HTTPS"
	@echo "3. Configurer un reverse proxy"
	@echo "4. Activer les sauvegardes automatiques"
