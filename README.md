<p align="center">
    <img src="https://raw.githubusercontent.com/projectcitybuild/web/refs/heads/main/resources/images/logo-2x.png" alt="Project City Build"/>
</p>

<p align="center">
    <a href="https://opensource.org/licenses/MPL-2.0"><img src="https://img.shields.io/badge/License-MPL%202.0-brightgreen.svg" alt="License: MPL 2.0"></a>
    <a href="https://github.com/projectcitybuild/web/actions/workflows/test.yml"><img src="https://github.com/projectcitybuild/web/actions/workflows/test.yml/badge.svg" alt="Build status"></a>
</p>

---

The official repository for [Project City Build](https://projectcitybuild.com)'s homepage and related web services.

### Stack
* Backend: Laravel 11
* Frontend: Laravel Blade, Vue 3
* CI/CD: GitHub Actions

All branches, commits and pull-requests are continuously tested

## Can I contribute?

Absolutely. Feel free to fork and send pull requests any time - we'd be thrilled to have some help.

---

## Development

The development environment uses [Dev Containers](https://containers.dev/) in combination with Docker.

### Dev Environment

Ensure you have the following installed:

* [Docker](https://docs.docker.com/get-docker/) 
* [VSCode](https://code.visualstudio.com/)
* The [Dev Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers) extension for VSCode

> [!IMPORTANT]
Before opening this project in VSCode, copy the env example file: `cp .env.example .env`

Open the project in VSCode. When the corner pop up appears, click the button to re-open the project in a container.

Access the site at http://localhost

### Accounts

* Admin account (email: `admin@pcbmc.co`, password: `test`)
* Regular account (email: `member@pcbmc.co`, password: `test`)

### 2FA

By default, no real authenticator is required. For accounts that have 2FA enabled, the code is always `000000` (6 zeroes).

To use a real authenticator, set `TOTP_BYPASS=false` in `.env`

### Captcha

By default, the Captcha will always pass on client and server side.

If you wish to test different situations, set `CAPTCHA_SITE_KEY` and `CAPTCHA_SECRET_KEY` in `.env`
to an appropriate value from the below list.

https://developers.cloudflare.com/turnstile/troubleshooting/testing/

### Mail

In local development, emails are forwarded to Mailpit instead of a real mail server.

Mail can be viewed at http://localhost:8025

### Debugging

Laravel Telescope is available at http://localhost/telescope for non-production environments.

If for some reason you don't want this, set `TELESCOPE_ENABLED=false` in `.env`

### Payments

Stripe CLI is installed as a container. 

It redirects Stripe webhook events to your local containers, which normally wouldn't be possible without
actually hosting the website somewhere public. This effectively allows us to complete a Stripe Checkout 
(eg. to test payments) and receive the payment data.

Ensure you set a name for your dev machine in `.env`

```
STRIPE_CLI_DEVICE_NAME=my-local-pc
```

Then run `docker-compose exec stripe-cli stripe login` in the `.docker` folder while the containers are running.

Follow the prompts to authenticate.

From now on you can complete a payment normally. For example, go to the donation page and complete
a purchase with a [mock credit card](https://docs.stripe.com/testing).

Authentication needs to be done every 90 days due to token expiry.

## Docker Deployment

Local development is defined by `.devcontainer/compose.yaml` and `.devcontainer/compose.dev.yaml`; it includes the shared services from `.docker/compose.yaml` and adds source mounts and development tools. Staging and production share the server overlay in `.docker/deployment/compose.server.yaml`; CI-built PHP-FPM and nginx images run without source mounts. Caddy configuration is in `.docker/images/proxy/`.

### Server Setup

Use one Linux server with Docker Engine, Docker Compose v2.17 or newer, `flock`, and an SSH user that can run Docker. Allow inbound ports 80 and 443, and point both environment domains at this server. Caddy handles HTTPS certificates and routes each domain to its environment.

Set the `DEPLOY_PATH` GitHub Environment secret to the same absolute path for both environments, for example `/opt/pcb-web/deploy`. The SSH user must be able to write that directory and its parent. Create these host files with mode `0600` before the first deployment:

Create `/etc/pcb-web` with mode `0700`, owned by the SSH deployment user, so Compose can read the files without making them available to other host users.

* `/etc/pcb-web/staging.env`
* `/etc/pcb-web/production.env`
* `/etc/pcb-web/staging.db-root-password`
* `/etc/pcb-web/production.db-root-password`
* `/etc/pcb-web/proxy.env`

The app files use Laravel's dotenv format and must contain the environment's `APP_KEY`, `APP_ENV`, `APP_DEBUG`, `APP_URL`, database credentials (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`), Redis settings, mail settings, and any integration credentials used by that environment. Keep the staging and production database names and credentials separate. Set `APP_DEBUG=false` in production. Each `.db-root-password` file contains only that environment's MariaDB root password; Compose mounts it into the database container and does not expose it to Laravel. The proxy file must contain `ACME_EMAIL` for certificate issuance.

### GitHub Environments

Create GitHub Environments named `staging` and `production`. Set `APP_DOMAIN` as an environment variable in each. Set these environment secrets in both environments:

* `DEPLOY_HOST`, `DEPLOY_USER`, and `DEPLOY_PATH` for the shared server and deployment directory
* `DEPLOY_PRIVATE_KEY` and `DEPLOY_KNOWN_HOSTS` for verified SSH access
* `GHCR_USERNAME` and `GHCR_PULL_TOKEN`, where the token has read access to the private GitHub Container Registry package

CI uses `GITHUB_TOKEN` with package write permission to publish the PHP-FPM and nginx images to `ghcr.io/<owner>/<repository>`, tagged with the commit SHA. The server pulls those images during deployment; runtime app and database secrets are read from the host env files and are never built into the images. The registry token is sent over SSH stdin for login and removed by `docker logout` after the deployment.

Run **Deploy Staging** first. After it passes its health checks, copy the commit SHA from the workflow summary into the `image-tag` input for **Deploy Production**. Production reruns the tests for that commit, promotes the exact same GHCR images without rebuilding them, then creates the requested Git tag and release. A failed candidate is removed without changing the active route; a failure during proxy activation restores the prior route and scheduler. Keep schema migrations backward-compatible with the previous app image because a database migration itself is not automatically reversed.
