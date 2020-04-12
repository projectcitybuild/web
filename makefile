.DEFAULT_GOAL := help

DIR_SRC="$(CURDIR)/src"
DIR_LARADOCK="$(CURDIR)/laradock"

PATH_TO_ENV = "$(DIR_SRC)/.env"

help:
	@echo ""
	@echo "Available tasks:"
	@echo "    bootstrap            Prepares your environment for first-time use"
	@echo "    install              Downloads all required JS and PHP dependencies"
	@echo "    container            Creates a docker-compose container for local dev"
	@echo ""

bootstrap:
	@echo "Preparing first-time installation"
	./scripts/bootstrap.sh
	cd $(DIR_SRC)

install:
	@echo "Installing dependencies"
	make container

container:
	@echo "Preparing container..."
	cd $(DIR_LARADOCK) && \
	docker-compose up -d nginx mariadb redis && \
	docker-compose exec workspace bash

container-windows:
	@echo "Preparing container..."
	cd $(DIR_LARADOCK) && \
	docker-compose up -d nginx mysql redis && \
	docker-compose exec workspace bash