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

* Docker Engine 20.10+ (or a current Docker Desktop release) with the Compose plugin 2.20+ (`docker compose version`). The Dev Container Compose file uses `include`, which requires Compose 2.20 or newer.
* [VSCode](https://code.visualstudio.com/)
* The [Dev Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers) extension for VSCode

> [!IMPORTANT]
Before opening this project in VSCode, copy the env example file: `cp .env.example .env`

Open the project in VSCode. When the corner pop up appears, click the button to re-open the project in a container.

Access the site at http://localhost

The post-create hook installs Composer and npm dependencies, generates the local application key, runs migrations and seeders, and builds frontend assets. In the Dev Container terminal, run `php artisan migrate` after adding migrations, `php artisan db:seed` to reseed, `npm run dev` for Vite's development server, or `npm run build` for a production asset build.

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

Stripe CLI is available as an opt-in development-only container. It forwards Stripe test webhooks to the local app; it is not part of the production Compose configuration.

It redirects Stripe webhook events to your local containers, which normally wouldn't be possible without
actually hosting the website somewhere public. This effectively allows us to complete a Stripe Checkout 
(eg. to test payments) and receive the payment data.

Set a Stripe test secret key and a name for your dev machine in the root `.env` file:

```
STRIPE_CLI_DEVICE_NAME=my-local-pc
STRIPE_SECRET=sk_test_...
```

From a host terminal at the repository root, start the forwarding service:

```sh
docker compose -f .devcontainer/compose.yaml --profile stripe up -d stripe-cli
docker compose -f .devcontainer/compose.yaml logs -f stripe-cli
```

The log shows a `whsec_...` signing secret. Set it as `STRIPE_WEBHOOK_SECRET` in `.env` so Laravel can verify forwarded events, then recreate the app containers to apply the updated environment: `docker compose -f .devcontainer/compose.yaml up -d --force-recreate fpm queue scheduler`.

Stop forwarding with `docker compose -f .devcontainer/compose.yaml --profile stripe stop stripe-cli`. The host command is needed because the Dev Container does not mount the Docker socket.

From now on you can complete a payment normally. For example, go to the donation page and complete
a purchase with a [mock credit card](https://docs.stripe.com/testing).

Authentication needs to be done every 90 days due to token expiry.

## Docker Deployment

Local development is defined by `.devcontainer/compose.yaml` and `.devcontainer/compose.dev.yaml`; it includes the shared services from `.docker/compose.yaml` and adds source mounts and development tools. Staging and production share the server overlay in `.docker/deployment/compose.server.yaml`; CI-built PHP-FPM and nginx images run without source mounts. Caddy configuration is in `.docker/images/proxy/`.

### Server Deployment

1. Set up `/etc/pcb-web` as mode `0700`, owned by the deployment user. 
2. Create `staging.env`, `production.env`, `staging.db-root-password`, `production.db-root-password`, and `proxy.env` there with mode `0600`. 
    * `proxy.env` must contain `ACME_EMAIL`. 
3. Configure GitHub `staging` and `production` environments with `APP_DOMAIN` plus `DEPLOY_HOST`, `DEPLOY_USER`, `DEPLOY_PATH`, `DEPLOY_PRIVATE_KEY`, `DEPLOY_KNOWN_HOSTS`, `GHCR_USERNAME`, and `GHCR_PULL_TOKEN` secrets. Use the same absolute `DEPLOY_PATH` in both environments.
