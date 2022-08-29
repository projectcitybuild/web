.DEFAULT_GOAL := help

SHELL=/bin/bash

.PHONY: help
help:
	@echo ""
	@echo "Available tasks:"
	@echo "    bootstrap            Prepares your environment for first-time use"
	@echo "    watch                Boots up the container if necessary and auto-refreshes localhost:3000 when files change"
	@echo ""

.PHONY: bootstrap
bootstrap:
	@./scripts/bootstrap.sh

watch:
	@./scripts/watch.sh
