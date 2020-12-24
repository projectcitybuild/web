.DEFAULT_GOAL := help

SHELL=/bin/bash

.PHONY: help
help:
	@echo ""
	@echo "Available tasks:"
	@echo "    bootstrap            Prepares your environment for first-time use"
	@echo "    container            Creates a docker-compose container for local dev"
	@echo ""

.PHONY: bootstrap
bootstrap:
	@./scripts/bootstrap.sh
