export UID=$(shell id -u)
export GID=$(shell id -g)

include .env
-include .env.local
export

DOCKER_COMPOSE_OPTIONS := -f docker-compose.yaml
DOCKER_COMPOSE         := docker compose $(DOCKER_COMPOSE_OPTIONS)
PHP                    := $(DOCKER_COMPOSE) run --rm php php
PHP_CONTAINER_SHELL    := $(DOCKER_COMPOSE) run --rm php
COMPOSER_BIN           := $(DOCKER_COMPOSE) run --rm php composer
BIN_CONSOLE            := $(PHP) bin/console

##
## Переключение окружений
## -----

env-test:
	$(eval BIN_CONSOLE += --env=test)
	@:
.PHONY: env-test

##
## Проект
## ------

cert:
	mkcert -install
	mkcert -cert-file ./docker/traefik/symfony-template.localhost.crt -key-file ./docker/traefik/symfony-template.localhost.key "*.symfony-template.localhost"
.PHONY: cert

hosts:
	sudo sh -c "echo '127.0.0.1 api.symfony-template.localhost' >> /etc/hosts"
	sudo sh -c "echo '127.0.0.1 traefik.symfony-template.localhost' >> /etc/hosts"
.PHONY: hosts

generate-jwt-keypair: var vendor
	$(BIN_CONSOLE) lexik:jwt:generate-keypair
.PHONY: generate-jwt-keypair

init: cert hosts generate-jwt-keypair
.PHONY: init

var:
	mkdir -p var
.PHONY: var

vendor:
	$(COMPOSER_BIN) install
.PHONY: vendor

update: ## Обновить пакеты
	$(COMPOSER_BIN) update
	$(COMPOSER_BIN) bump
	touch vendor
.PHONY: update

up: var vendor db ## Запустить приложение
	$(DOCKER_COMPOSE) up --build --remove-orphans --detach
.PHONY: up

down: ## Остановить приложение
	$(DOCKER_COMPOSE) down --remove-orphans
.PHONY: stop

##
## БД
## ------

db: vendor ## Создать базу данных и обновить схему
	$(PHP_CONTAINER_SHELL) pg_isready -h $(DB_HOST) -p $(DB_PORT) -d $(DB_NAME)
	$(BIN_CONSOLE) doctrine:database:create --if-not-exists
	$(BIN_CONSOLE) doctrine:migrations:migrate --no-interaction
.PHONY: db

db-rollback: vendor ## Откатить последнюю миграцию
	$(BIN_CONSOLE) doctrine:migrations:migrate prev --no-interaction
.PHONY: db-rollback

db-drop: vendor ## Удалить базу
	$(PHP_CONTAINER_SHELL) pg_isready -h $(DB_HOST) -p $(DB_PORT) -d $(DB_NAME)
	$(BIN_CONSOLE) doctrine:database:drop --if-exists --force --no-interaction
.PHONY: db-drop

migrations: vendor ## Создать миграцию
	$(BIN_CONSOLE) doctrine:migrations:diff --allow-empty-diff --no-interaction
	git add migrations
.PHONY: migrations

schema-validate: vendor ## Проверяет, что схема бд полностью соответствует схеме приложения
	$(BIN_CONSOLE) schema:validate
.PHONY: schema-validate

##
## Качество кода
## ------

check: rector cs psalm yaml-lint deptrac container schema-validate composer ## Запустить все проверки качества кода
.PHONY: check

psalm: var vendor ## Запустить полный статический анализ PHP кода при помощи Psalm (https://psalm.dev/)
	$(PHP) tools/psalm/vendor/bin/psalm --no-diff
.PHONY: psalm

cs: var vendor ## Проверить PHP code style при помощи PHP CS Fixer (https://github.com/FriendsOfPHP/PHP-CS-Fixer)
	PHP_CS_FIXER_IGNORE_ENV=1 $(PHP) vendor/bin/php-cs-fixer fix --dry-run --diff --using-cache=yes -v
.PHONY: cs

fixcs: var vendor ## Исправить ошибки PHP code style при помощи PHP CS Fixer (https://github.com/FriendsOfPHP/PHP-CS-Fixer)
	PHP_CS_FIXER_IGNORE_ENV=1 $(PHP) vendor/bin/php-cs-fixer fix --using-cache=yes -v
.PHONY: fixcs

rector: var vendor ## Запустить полный анализ PHP кода при помощи Rector (https://getrector.org)
	$(PHP) vendor/bin/rector process --dry-run
.PHONY: rector

rector-fix: var vendor ## Запустить исправление PHP кода при помощи Rector (https://getrector.org)
	$(PHP) vendor/bin/rector process
.PHONY: rector-fix

yaml-lint: vendor ## Проверить YAML-файлы при помощи Symfony YAML linter (https://symfony.com/doc/current/components/yaml.html#syntax-validation)
	$(BIN_CONSOLE) lint:yaml config --parse-tags
.PHONY: yaml-lint

container: container-lint container-prod ## Проверить DI-контейнер
.PHONY: container

container-lint: vendor ## Проверить DI-контейнер при помощи Symfony Container linter (https://symfony.com/doc/current/service_container.html#linting-service-definitions)
	$(BIN_CONSOLE) lint:container
.PHONY: container-lint

container-prod: vendor ## Проверить, что DI-контейнер успешно собирается в env=prod
	$(BIN_CONSOLE) cache:clear --env=prod
.PHONY: container-prod

composer: composer-validate composer-require composer-unused composer-audit composer-normalize ## Запустить все проверки Composer
.PHONY: composer

composer-validate: ## Провалидировать composer.json и composer.lock при помощи composer validate (https://getcomposer.org/doc/03-cli.md#validate)
	$(COMPOSER_BIN) validate --strict --no-check-publish
.PHONY: composer-validate

composer-require: vendor ## Обнаружить неявные зависимости от внешних пакетов при помощи ComposerRequireChecker (https://github.com/maglnet/ComposerRequireChecker)
	$(PHP) vendor/bin/composer-require-checker check --config-file=composer-require-checker.json
.PHONY: composer-require

composer-unused: vendor ## Обнаружить неиспользуемые зависимости Composer при помощи composer-unused (https://github.com/icanhazstring/composer-unused)
	$(PHP) vendor/bin/composer-unused
.PHONY: composer-unused

composer-audit: ## Обнаружить уязвимости в зависимостях Composer при помощи composer audit (https://getcomposer.org/doc/03-cli.md#audit)
	$(COMPOSER_BIN) audit
.PHONY: composer-audit

composer-normalize: ## Нормализация composer.json. Анализ (https://github.com/ergebnis/composer-normalize)
	$(COMPOSER_BIN) normalize --dry-run
.PHONY: composer-normalize

composer-normalize-fix: ## Нормализация composer.json (https://github.com/ergebnis/composer-normalize)
	$(COMPOSER_BIN) normalize
.PHONY: composer-normalize-fix

deptrac: deptrac-directories deptrac-layers deptrac-features ## Проверить все зависимости (https://github.com/qossmic/deptrac)
.PHONY: deptrac

deptrac-directories: var vendor ## Проверить зависимости директорий (https://github.com/qossmic/deptrac)
	$(PHP) vendor/bin/deptrac analyze --config-file=deptrac.directories.yaml --cache-file=var/.deptrac.directories.cache
.PHONY: deptrac-directories

deptrac-layers: var vendor ## Проверить зависимости слоев (https://github.com/qossmic/deptrac)
	$(PHP) vendor/bin/deptrac analyze --config-file=deptrac.layers.yaml --cache-file=var/.deptrac.layers.cache
.PHONY: deptrac-layers

deptrac-features: var vendor ## Проверить зависимости в фичах (https://github.com/qossmic/deptrac)
	$(PHP) vendor/bin/deptrac analyze --config-file=deptrac.features.yaml --cache-file=var/.deptrac.features.cache
.PHONY: deptrac-features

##
## Тесты
## ------

tests: tests-unit tests-integration tests-functional ## Запустить все тесты
.PHONY: tests

tests-unit: env-test var vendor ## Запустить юнит тесты PHPUnit (https://phpunit.de/)
	$(PHP) vendor/bin/phpunit --group=unit --exclude-group=legacy
.PHONY: tests-unit

tests-integration: env-test var vendor db-drop db ## Запустить интеграционные тесты PHPUnit (https://phpunit.de/)
	$(PHP) vendor/bin/phpunit --group=integration --exclude-group=legacy
.PHONY: tests-integration

tests-functional: env-test var vendor fixtures ## Запустить функциональные тесты PHPUnit (https://phpunit.de/)
	$(PHP) vendor/bin/phpunit --group=functional --exclude-group=legacy
.PHONY: tests-functional

##
## Фикстуры
## ------

fixtures: var vendor db-drop db ## Загрузить фикстуры в БД
	$(BIN_CONSOLE) fixtures:user
.PHONY: fixtures