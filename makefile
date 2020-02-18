.DEFAULT_GOAL := help

DIR_SRC="$(CURDIR)/src"
DIR_LARADOCK="$(CURDIR)/laradock"

PATH_TO_ENV = "$(DIR_SRC)/.env"

help:
	@echo ""
	@echo "Available tasks:"
	@echo "    install              Prepares your environment for first-time use"
	@echo "    container            Creates a docker-compose container for local dev"
	@echo ""

install:
	cd $(DIR_SRC)

container:
	@echo "Preparing container..."
	cd $(DIR_LARADOCK) && \
	docker-compose up -d nginx mariadb redis && \
	docker-compose exec workspace bash