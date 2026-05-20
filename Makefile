-include .env
export

.SILENT:

.PHONY: help up down restart build status logs shell composer test db-shell db-export db-import clean certs fresh

default: help

PROJECT_NAME := phpapipro
DOMAIN := $(PROJECT_NAME).dev.localhost

CERT_DIR := ./docker/nginx/certs
CERT_FILE := $(CERT_DIR)/$(DOMAIN)+3.pem
CERT_KEY := $(CERT_DIR)/$(DOMAIN)+3-key.pem

COMPOSE := docker compose
PHP_SERVICE := php
DB_SERVICE := db

setup:
	mkdir -p $(CERT_DIR)

ssl: setup
	mkcert -install
	cd $(CERT_DIR) && mkcert $(DOMAIN) "*.dev.localhost" localhost 127.0.0.1

up: ## Starts all service containers in the background
	@echo "🚀 Climbing the containers..."
	$(COMPOSE) up -d

down: ## Stop and remove containers and anonymous volumes
	@echo "🛑 Stopping the containers..."
	$(COMPOSE) down --remove-orphans

restart: ## Restart all containers
	@echo "🔄 Restarting containers..."
	$(COMPOSE) restart

build: ## Rebuild Docker images without caching
	@echo "🛠️ Building Docker images from scratch..."
	$(COMPOSE) build --no-cache

clean: ## Stops everything and cleans all temporary data/volumes
	@echo "💣 Wiping containers and temporary volumes..."
	$(COMPOSE) down -v --remove-orphans

status: ## Lists containers and their current status
	@echo "📊 Container status:"
	$(COMPOSE) ps

logs: ## Displays the logs of all services in real time
	@echo "📜 Tracking the logs... (Press Ctrl+C to exit)"
	$(COMPOSE) logs -f

shell: ## Accesses the PHP container's terminal
	@echo "💻 Accessing the PHP container terminal..."
	$(COMPOSE) exec $(PHP_SERVICE) sh

composer: ## Run Composer inside the PHP container. Ex: make composer ARGS="install"
	@echo "📦 Running Composer: composer $(ARGS)"
	$(COMPOSE) exec $(PHP_SERVICE) composer $(ARGS)

db-shell: ## Accesses the MariaDB command line client
	@echo "🗃️ Accessing the MariaDB shell..."
	$(COMPOSE) exec $(DB_SERVICE) mariadb -u"$(MYSQL_USER)" -p"$(MYSQL_PASSWORD)" "$(MYSQL_DATABASE)"

help: ## Displays this help message with all available commands
	@awk 'BEGIN {FS = ":.*##"; printf "\nUso:\n  make \033[36m<comando>\033[0m\n\nComandos:\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2 }' $(MAKEFILE_LIST)