.DEFAULT_GOAL := help

SHELL=/bin/bash

.PHONY: help
help:
	@echo ""
	@echo "Available tasks:"
	@echo "    bootstrap            Prepares your environment for first-time use"
	@echo "    cert                 Generates a SSL certificate for use with local dev"
	@echo "    shell                Enters the php-fpm container (with Sh shell)"
	@echo "    stripe payment       Creates a (test) Stripe payment and forwards the event to our webhook API"
	@echo "    test                 Runs phpunit tests in the php-fpm container"
	@echo "    watch                Runs browsersync with hotloading and file watching"
	@echo ""

.PHONY: bootstrap
bootstrap:
	@./scripts/bootstrap.sh

.PHONY: cert
cert:
	@./scripts/generate-cert.sh

.PHONE: shell
workspace:
	@./scripts/shell.sh

.PHONY: test
test:
	@./scripts/test.sh

.PHONE: watch
watch:
	@./scripts/browsersync.sh
