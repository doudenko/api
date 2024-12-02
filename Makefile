.PHONY: help up down ps shell install update test
.DEFAULT_GOAL   	= help
SHELL 		    	= /bin/bash
ENV_PATH	    	= .env
UID             	= $(shell id -u)
GID             	= $(shell id -g)
LIST            	= $(firstword $(MAKEFILE_LIST))
DOCKER_RUN      	= ${DOCKER_COMPOSE} exec php
DOCKER_RUN_AS_USER	= ${DOCKER_COMPOSE} exec --user ${UID}:${UID} php

include ${ENV_PATH}

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' ${LIST} | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

up: ## Запуск контейнеров
	@${DOCKER_COMPOSE} up --build -d --remove-orphans

down: ## Остановка контейнеров
	@${DOCKER_COMPOSE} down --remove-orphans

ps: ## Просмотр статуса контейнеров
	@${DOCKER_COMPOSE} ps

shell: ## Вход в контейнер app
	@${DOCKER_RUN_AS_USER} bash

install: ## Запуск composer install
	@${DOCKER_RUN_AS_USER} composer install --prefer-dist

update: ## Запуск composer update
	@${DOCKER_RUN_AS_USER} composer update --prefer-dist

test: ## Запуск юнит-тестов и проверки кодстайла
	@${DOCKER_RUN} composer run-script test
