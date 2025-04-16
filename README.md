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
* Frameworks: Laravel 11, Vue 3
* Environment: Laravel Sail (Docker)
* CI/CD: GitHub Actions

All branches, commits and pull-requests are continuously tested

## Can I contribute?

Absolutely. Feel free to fork and send pull requests any time - we'd be thrilled to have some help.

---

## Development

### Dev Environment

Ensure you have [Docker](https://docs.docker.com/get-docker/) installed.

If this is your first time building the containers:

1. Copy the env example file: `cp .env.example .env`
2. Run `sail up -d` to start the containers
3. Run `sail composer install` to install PHP dependencies
4. Run `sail npm install` to install frontend dependencies
5. Run `sail artisan key:generate` to generate an app key (for encryption, etc)
6. Run `sail artisan migrate --seed`

From then onwards:

1. Run `sail up -d` if containers aren't already running
2. Run `sail npm run dev` to continuously build the frontend

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

Laravel Sail uses Mailpit during local development to avoid sending real emails.

Mail can be viewed at http://localhost:8025

### Debugging

Laravel Telescope is available at http://localhost/telescope for non-production environments.

If for some reason you don't want this, set `TELESCOPE_ENABLED=false` in `.env`

### Performance Monitoring

Laravel Pulse is available at http://localhost/pulse if logged-in as an admin account.

Data gathering is only enabled if you have `PULSE_ENABLED=true` in `.env`.
This is `false` by default, as this is not particularly useful in a local environment.

### Payments

Stripe CLI is installed as a container. 

It redirects Stripe webhook events to your local containers, which normally wouldn't be possible without
actually hosting the website somewhere public. This effectively allows us to complete a Stripe Checkout 
(eg. to test payments) and receive the payment data.

Ensure you set a name for your dev machine in `.env`

```
STRIPE_CLI_DEVICE_NAME=my-local-pc
```

Then run `docker-compose exec stripe-cli stripe login` while the containers are running.

Follow the prompts to authenticate.

From now on you can complete a payment normally. For example, go to the donation page and complete
a purchase with a [mock credit card](https://docs.stripe.com/testing).

Authentication needs to be done every 90 days due to token expiry.
