.DEFAULT_GOAL := help

DIR_SRC="$(CURDIR)/src"
DIR_LARADOCK="$(CURDIR)/laradock"

help:
	@echo ""
	@echo "Available tasks:"
	@echo "    bootstrap            Prepares your environment for first-time use"
	@echo "    start              	Creates a docker-compose container for local dev"
	@echo "    container            Creates and enters a docker-compose container for local dev"
	@echo "    test   				Runs unit and linter tests in the container"
	@echo ""

bootstrap:
	@echo "Preparing first-time installation"
	./scripts/bootstrap.sh
	cd $(DIR_SRC)

start:
	docker-compose up -d nginx mariadb redis && \

container:
	cd $(DIR_LARADOCK) && \
	docker-compose up -d nginx mariadb redis && \
	docker-compose exec workspace bash

# TODO: find a way to merge this into the `container` target
container-windows:
	cd $(DIR_LARADOCK) && \
	docker-compose up -d nginx mysql redis && \
	docker-compose exec workspace bash

test:
	cd $(DIR_LARADOCK) && \
	docker-compose exec workspace ./vendor/bin/phpstan analyse -c phpstan.neon
	docker-compose exec workspace ./vendor/bin/phpunit