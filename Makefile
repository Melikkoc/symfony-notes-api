PROJECT=backend
DC=docker compose -p $(PROJECT)

.PHONY: help

help:
	@echo ""
	@echo "Available commands:"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:' Makefile | grep -v '%' | sed 's/:.*//'
	@echo ""

DEV_CONSOLE=$(DC) exec php-dev php bin/console
PROD_CONSOLE=$(DC) exec php php bin/console

.PHONY: dev prod

# =====================
# DEV (Shortcut: make dev <symfony-command>)
# =====================
dev:
	@$(DEV_CONSOLE) $(filter-out $@,$(MAKECMDGOALS))

# ---------------------
# DEV FIXED COMMANDS
# ---------------------
dev-console:
	$(DC) exec php-dev php bin/console

dev-up:
	$(DC) --profile dev up -d

dev-build:
	$(DC) --profile dev up --build -d

dev-logs:
	$(DC) --profile dev logs -f

dev-shell:
	$(DC) exec php-dev sh

dev-migrate:
	$(DEV_CONSOLE) doctrine:migrations:migrate

dev-mig-status:
	$(DEV_CONSOLE) doctrine:migrations:status

dev-db:
	$(DC) exec db-dev psql -U notes -d notes


# =====================
# PROD (Shortcut: make prod <symfony-command>)
# =====================
prod:
	@$(PROD_CONSOLE) $(filter-out $@,$(MAKECMDGOALS))

# ---------------------
# PROD FIXED COMMANDS
# ---------------------
prod-console:
	$(DC) exec php php bin/console

prod-up:
	$(DC) --profile prod up -d

prod-build:
	$(DC) --profile prod up --build -d

prod-logs:
	$(DC) --profile prod logs -f

prod-shell:
	$(DC) exec php sh

prod-migrate:
	@if [ "$(CONFIRM)" != "YES" ]; then \
		echo "❌ REFUSED: PROD migration requires confirmation"; \
		echo "👉 Run: CONFIRM=YES make prod-migrate"; \
		exit 1; \
	fi
	$(PROD_CONSOLE) doctrine:migrations:migrate

prod-mig-status:
	$(PROD_CONSOLE) doctrine:migrations:status

prod-db:
	$(DC) exec db-prod psql -U notes -d notes
	
# =====================
# DB QUERIES
# =====================

.PHONY: dev-query prod-query

dev-query:
	$(DC) --profile dev exec db-dev psql -U notes -d notes -c "$(SQL)"

prod-query:
	$(DC) --profile prod exec db-prod psql -U notes -d notes -c "$(SQL)"

# =====================
# GLOBAL COMMANDS
# =====================

.PHONY: down

down:
	$(DC) down

.PHONY: nuke

nuke:
	@if [ "$(CONFIRM)" != "YES" ]; then \
		echo "❌ REFUSED: This will DELETE ALL VOLUMES (PROD DATA!)"; \
		echo "👉 Run: CONFIRM=YES make nuke"; \
		exit 1; \
	fi
	$(DC) down -v

# ---------------------
# Dummy rule (REQUIRED)
# ---------------------
%:
	@: