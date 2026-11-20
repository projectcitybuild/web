# Docker deployment

## Development setup

Local development uses `.devcontainer/compose.yaml` and `.devcontainer/compose.dev.yaml`. These files include the shared services from `.docker/compose.yaml` and add source-code mounts and development tools.

Staging and production use `.docker/deployment/compose.server.yaml`. CI builds the PHP-FPM and nginx images. These images run without source-code mounts. Caddy settings are in `.docker/images/proxy/`.

## Server setup

1. Create `/etc/pcb-web`. Set its owner to the deployment user and its mode to `0700`.
2. In that directory, create these files and set each file's mode to `0600`:
   - `staging.env`
   - `production.env`
   - `staging.db-root-password`
   - `production.db-root-password`
   - `proxy.env`
3. Add `ACME_EMAIL` to `proxy.env`.
4. Configure the GitHub `staging` and `production` environments:
   - Set `APP_DOMAIN`.
   - Add these secrets: `DEPLOY_HOST`, `DEPLOY_USER`, `DEPLOY_PATH`, `DEPLOY_PRIVATE_KEY`, `DEPLOY_KNOWN_HOSTS`, `GHCR_USERNAME`, and `GHCR_PULL_TOKEN`.
   - Use the same absolute `DEPLOY_PATH` in both environments.

## Healthchecks.io

1. Create a regular heartbeat monitor named `Web - Redis Queue Worker`. Set the period to 5 minutes, the grace period to 2-3 minutes, and the timezone to UTC.
2. Create a regular heartbeat monitor named `Web - Daily Mail Canary`. Set the period to 24 hours, the grace period to 2-4 hours, and the timezone to UTC.
3. In deployment secrets, set `HEALTHCHECK_URL_QUEUE`, `HEALTHCHECK_URL_MAIL`, and `HEALTHCHECK_MAIL_TO`. Use a dedicated monitoring inbox for `HEALTHCHECK_MAIL_TO`.
4. Keep `HEALTHCHECK_MAIL_ENABLED=false` at first. Do not commit ping URLs or credentials.
5. After setup, enable the daily canary by setting `HEALTHCHECK_MAIL_ENABLED=true`.
