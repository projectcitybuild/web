.DEFAULT_GOAL := help

SHELL=/bin/bash

.PHONY: help
help:
	@echo ""
	@echo "Available tasks:"
	@echo "    bootstrap            Prepares your environment for first-time use"
	@echo "    test                 Runs phpunit tests in the php-fpm container"
	@echo ""

.PHONY: bootstrap
bootstrap:
	@./scripts/bootstrap.sh

.PHONY: test
test:
	@./scripts/test.sh
