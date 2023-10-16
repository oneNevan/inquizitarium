# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor sf cc check lint fixcs psalm deptrac-directories deptrac-layers deptrac-modules test test-reset

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the PHP FPM container
	@$(PHP_CONT) sh

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf

## —— Quality Tools ————————————————————————————————————————————————————————————
check: cc lint psalm deptrac-directories deptrac-layers deptrac-modules test ## Run all checks and tests

lint: ## Check code via linter (PHP CS FIXER)
	@$(PHP_CONT) vendor/bin/php-cs-fixer fix --dry-run --diff --verbose

fixcs: ## Fix code via PHP CS FIXER
	@$(PHP_CONT) vendor/bin/php-cs-fixer fix --diff --verbose

psalm: ## Check code via static-analyzer Psalm
	@$(PHP_CONT) vendor/bin/psalm --no-diff $(file)

deptrac-directories: ## Check directories dependencies
	@$(PHP_CONT) vendor/bin/deptrac analyze --config-file=deptrac-directories.yaml --cache-file=var/.deptrac-directories.cache

deptrac-layers: ## Check layers dependencies
	@$(PHP_CONT) vendor/bin/deptrac analyze --config-file=deptrac-layers.yaml --cache-file=var/.deptrac-layers.cache

deptrac-modules: ## Check modules dependencies
	@$(PHP_CONT) vendor/bin/deptrac analyze --config-file=deptrac-modules.yaml --cache-file=var/.deptrac-modules.cache

test: ## Execute tests
	@$(PHP_CONT) bin/phpunit

test-reset: ## Execute tests with resetting test database
	@$(PHP_CONT) bin/console doctrine:database:drop --env=test --if-exists --force
	@$(PHP_CONT) bin/console doctrine:database:create --env=test
	@$(PHP_CONT) bin/console doctrine:migrations:migrate --env=test --no-interaction
	@$(PHP_CONT) bin/console doctrine:fixtures:load --env=test --append
	@$(PHP_CONT) bin/phpunit
