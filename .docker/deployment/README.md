## Docker Deployment

Local development is defined by `.devcontainer/compose.yaml` and `.devcontainer/compose.dev.yaml`; it includes the shared services from `.docker/compose.yaml` and adds source mounts and development tools. Staging and production share the server overlay in `.docker/deployment/compose.server.yaml`; CI-built PHP-FPM and nginx images run without source mounts. Caddy configuration is in `.docker/images/proxy/`.

### Server Deployment

1. Set up `/etc/pcb-web` as mode `0700`, owned by the deployment user. 
2. Create `staging.env`, `production.env`, `staging.db-root-password`, `production.db-root-password`, and `proxy.env` there with mode `0600`. 
    * `proxy.env` must contain `ACME_EMAIL`. 
3. Configure GitHub `staging` and `production` environments with `APP_DOMAIN` plus `DEPLOY_HOST`, `DEPLOY_USER`, `DEPLOY_PATH`, `DEPLOY_PRIVATE_KEY`, `DEPLOY_KNOWN_HOSTS`, `GHCR_USERNAME`, and `GHCR_PULL_TOKEN` secrets. Use the same absolute `DEPLOY_PATH` in both environments.

### Healthchecks.io

1. Create a regular heartbeat monitor named `Web - Redis Queue Worker` with a 5-minute period, 2-3 minute grace period, and UTC timezone.
2. Create a regular heartbeat monitor named `Web - Daily Mail Canary` with a 24-hour period, 2-4 hour grace period, and UTC timezone.
3. Configure `HEALTHCHECK_URL_QUEUE`, `HEALTHCHECK_URL_MAIL`, and a dedicated monitoring inbox in `HEALTHCHECK_MAIL_TO` in deployment secrets. Keep `HEALTHCHECK_MAIL_ENABLED=false` initially; never commit ping URLs or credentials.
4. Enable the daily canary by setting `HEALTHCHECK_MAIL_ENABLED=true`.
