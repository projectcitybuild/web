<p align="center">
    <img src="https://github.com/projectcitybuild/web/blob/master/resources/images/logo-2x.png?raw=true" alt="Project City Build"/>
</p>

<p align="center">
    <a href="https://opensource.org/licenses/MPL-2.0"><img src="https://img.shields.io/badge/License-MPL%202.0-brightgreen.svg" alt="License: MPL 2.0"></a>
    <a href="https://github.com/projectcitybuild/web/actions/workflows/test.yml"><img src="https://github.com/projectcitybuild/web/actions/workflows/test.yml/badge.svg" alt="Build status"></a>
    <a href="https://github.com/projectcitybuild/web/actions/workflows/test.yml"><img src="https://github.com/projectcitybuild/web/actions/workflows/lint.yml/badge.svg" alt="Linter status"></a>
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

Run `sail up -d` to start the containers.

If this is your first time building the containers, additionally:

* Run `sail composer install` to install PHP dependencies
* Run `sail npm install` to install frontend dependencies

From then onwards:

Run `sail npm run dev` to (continuously) build the frontend

### Mail

Laravel Sail uses Mailpit during local development.

Mail can be viewed at http://localhost:8025


### Captcha

By default, the Captcha will always pass on client and server side.

If you wish to test different situations, set `CAPTCHA_SITE_KEY` and `CAPTCHA_SECRET_KEY` in `.env`
to an appropriate value from the below list.

https://developers.cloudflare.com/turnstile/troubleshooting/testing/

### 2FA

No real authenticator is required.

For accounts that have 2FA enabled, always use the code `0000`.
