SHELL=/bin/sh

DOCKER_COMP = docker exec vending-machine-app
COMPOSER = $(DOCKER_COMP) composer
CONSOLE = docker exec -it vending-machine-app php index.php

check-container:
	@docker compose ps | grep vending-machine-app | grep -q "Up" || (echo "vending-machine-app is not running. Please start the container with 'make start'." && exit 1)

test: check-container
	@docker exec -e APP_ENV=test vending-machine-app ./vendor/bin/phpunit --stop-on-error --stop-on-failure --colors=always $(if $(s),--testsuite $(s),) $(if $(f),--filter $(f),)

start:
	@docker compose up --build -d

stop:
	@docker compose stop

destroy:
	@docker compose down

init: start
	@make composer c="install"

composer: check-container
	@$(eval c ?=)
	@$(COMPOSER) $(c)

sh: check-container
	@docker exec -it vending-machine-app sh

console: check-container
	@$(CONSOLE) $(c)

operate: check-container
	@$(CONSOLE) vending-machine:operate

maintenance: check-container
	@$(CONSOLE) vending-machine:maintenance