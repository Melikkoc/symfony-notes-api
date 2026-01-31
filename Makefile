# ============================================================================
# Project configuration
# ============================================================================

# Docker Compose project name.
# Ensures all containers, networks and volumes are namespaced consistently.
PROJECT=symfony-notes-api

# Base docker compose command with fixed project name
DC=docker compose -p $(PROJECT)

# ============================================================================
# Database configuration (shared between dev & prod)
# ============================================================================

# PostgreSQL credentials (must match docker-compose.yaml)
DB_USER=notes
DB_NAME=notes

# Database container names per profile
DEV_DB_CONTAINER=db-dev
PROD_DB_CONTAINER=db-prod

# ============================================================================
# Helper / documentation
# ============================================================================

.PHONY: help

# Lists all available make targets
help:
	@echo ""
	@echo "Available commands:"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:' Makefile | grep -v '%' | sed 's/:.*//'
	@echo ""

# ============================================================================
# Symfony console shortcuts
# ============================================================================

# Symfony console inside DEV PHP container
DEV_CONSOLE=$(DC) exec php-dev php bin/console

# Symfony console inside PROD PHP container
PROD_CONSOLE=$(DC) exec php php bin/console

.PHONY: dev prod

# ============================================================================
# DEV shortcut
# Usage:
#   make dev doctrine:migrations:status
#   make dev cache:clear
# ============================================================================
dev:
	@$(DEV_CONSOLE) $(filter-out $@,$(MAKECMDGOALS))

# ============================================================================
# DEV fixed commands
# ============================================================================

# Open Symfony console in dev container
dev-console:
	$(DC) exec php-dev php bin/console

# Start dev stack (detached)
dev-up:
	$(DC) --profile dev up -d

# Build images and start dev stack
dev-build:
	$(DC) --profile dev up --build -d

# Follow logs of dev services
dev-logs:
	$(DC) --profile dev logs -f

# Open shell inside dev PHP container
dev-shell:
	$(DC) exec php-dev sh

# Run Doctrine migrations in dev (non-interactive)
dev-migrate:
	$(DEV_CONSOLE) doctrine:migrations:migrate --no-interaction

# Show migration status for dev database
dev-mig-status:
	$(DEV_CONSOLE) doctrine:migrations:status

# Open psql shell for dev database
dev-db:
	$(DC) exec $(DEV_DB_CONTAINER) psql -U $(DB_USER) -d $(DB_NAME)

# ============================================================================
# PROD shortcut
# Usage:
#   make prod doctrine:migrations:status
#   make prod cache:clear
# ============================================================================
prod:
	@$(PROD_CONSOLE) $(filter-out $@,$(MAKECMDGOALS))

# ============================================================================
# PROD fixed commands
# ============================================================================

# Open Symfony console in prod container
prod-console:
	$(DC) exec php php bin/console

# Start prod stack (detached)
prod-up:
	$(DC) --profile prod up -d

# Build images and start prod stack
prod-build:
	$(DC) --profile prod up --build -d

# Follow logs of prod services
prod-logs:
	$(DC) --profile prod logs -f

# Open shell inside prod PHP container
prod-shell:
	$(DC) exec php sh

# Run Doctrine migrations in prod
# Requires explicit confirmation to avoid accidental schema changes
prod-migrate:
	@if [ "$(CONFIRM)" != "YES" ]; then \
		echo "❌ REFUSED: PROD migration requires confirmation"; \
		echo "👉 Run: CONFIRM=YES make prod-migrate"; \
		exit 1; \
	fi
	$(PROD_CONSOLE) doctrine:migrations:migrate --no-interaction

# Show migration status for prod database
prod-mig-status:
	$(PROD_CONSOLE) doctrine:migrations:status

# Open psql shell for prod database
prod-db:
	$(DC) exec $(PROD_DB_CONTAINER) psql -U $(DB_USER) -d $(DB_NAME)

# ============================================================================
# Database queries (one-liners)
# Usage:
#   make dev-query SQL="SELECT * FROM note;"
#   make prod-query SQL="SELECT count(*) FROM note;"
# ============================================================================

.PHONY: dev-query prod-query

# Execute raw SQL against dev database
dev-query:
	$(DC) --profile dev exec $(DEV_DB_CONTAINER) psql -U $(DB_USER) -d $(DB_NAME) -c "$(SQL)"

# Execute raw SQL against prod database
prod-query:
	$(DC) --profile prod exec $(PROD_DB_CONTAINER) psql -U $(DB_USER) -d $(DB_NAME) -c "$(SQL)"

# ============================================================================
# Global Docker commands
# ============================================================================

.PHONY: down

# Stop all running containers (keeps volumes / data)
down:
	$(DC) down

.PHONY: nuke

# Stop containers AND delete all volumes
# This will permanently delete all database data (including prod!)
nuke:
	@if [ "$(CONFIRM)" != "YES" ]; then \
		echo "❌ REFUSED: This will DELETE ALL VOLUMES (PROD DATA!)"; \
		echo "👉 Run: CONFIRM=YES make nuke"; \
		exit 1; \
	fi
	$(DC) down -v

# ============================================================================
# Dummy rule
# Required so Make does not error on unknown arguments
# ============================================================================
%:
	@: