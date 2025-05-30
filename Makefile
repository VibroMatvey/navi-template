DOCKER_COMPOSE = docker compose
PHP_CONTAINER = php
COMPOSER = $(DOCKER_COMPOSE) exec $(PHP_CONTAINER) composer
SYMFONY = $(DOCKER_COMPOSE) exec $(PHP_CONTAINER) php bin/console

.PHONY: build up down restart logs install deps cache-clear migrate test

# Сборка контейнеров
build:
	$(DOCKER_COMPOSE) build

# Запуск приложения
up:
	$(DOCKER_COMPOSE) up -d

# Запуск приложения (dev)
dev:
	$(DOCKER_COMPOSE) up

# Остановка приложения
down:
	$(DOCKER_COMPOSE) stop

# Перезапуск приложения
restart: down up

# Просмотр логов
logs:
	$(DOCKER_COMPOSE) logs -f

# Установка зависимостей Composer
install:
	$(COMPOSER) install

# Обновление зависимостей
deps:
	$(COMPOSER) update

# Очистка кэша Symfony
cache-clear:
	$(SYMFONY) cache:clear

# Выполнение миграций
schema-update:
	$(SYMFONY) d:s:u -f

## Запуск тестов
#test:
#	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) php bin/phpunit