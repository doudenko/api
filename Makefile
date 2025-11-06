.PHONY: help up down ps shell install update test
.DEFAULT_GOAL   	= help

SHELL 		    	= /bin/bash
ENV_PATH	    	= .env
UID             	= $(shell id -u)
GID             	= $(shell id -g)
LIST            	= $(firstword $(MAKEFILE_LIST))
DOCKER_RUN      	= ${DOCKER_COMPOSE} exec application
DOCKER_RUN_AS_USER	= ${DOCKER_COMPOSE} exec --user ${UID}:${UID} application

include ${ENV_PATH}

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' ${LIST} | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

up: ## Запустить приложение. Произвести сборку Docker образов и запуск контейнеров необходимых для работы приложения
	@${DOCKER_COMPOSE} up --build -d --remove-orphans

down: ## Остановить приложение. Остановить запущенные контейнеры приложения и удалить Docker образы
	@${DOCKER_COMPOSE} down --remove-orphans

ps: ## Показать статус контейнеров
	@${DOCKER_COMPOSE} ps

shell: ## Запустить терминал командной строки в основном контейнере приложения
	@${DOCKER_RUN_AS_USER} ${SHELL}

install: ## Установить зависимости проекта
	@${DOCKER_RUN_AS_USER} composer install --no-cache --prefer-dist

update: ## Обновить зависимости проекта
	@${DOCKER_RUN_AS_USER} composer update --no-cache --prefer-dist

phpunit: ## Запустить PHPUnit
	@${DOCKER_RUN_AS_USER} composer run-script phpunit

phpstan: ## Запустить PHPStan
	@${DOCKER_RUN_AS_USER} composer run-script phpstan

test: ## Запустить тесты
	@${DOCKER_RUN_AS_USER} composer run-script test

run: ## Выполнить стартовый скрипт
	@${DOCKER_RUN_AS_USER} php public/index.php
