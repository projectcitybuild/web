<p align="center">
    <img src="https://projectcitybuild.com/assets/images/logo.png" alt="Project City Build"/>
</p>

<p align="center">
    <a href="https://opensource.org/licenses/MPL-2.0"><img src="https://img.shields.io/badge/License-MPL%202.0-brightgreen.svg" alt="License: MPL 2.0"></a>
    <a href="https://github.com/projectcitybuild/web/actions?query=workflow%3A%22PHP+Test%22"><img src="https://github.com/projectcitybuild/web/workflows/PHP%20Test/badge.svg?branch=dev" alt="Build status"></a>
    <a href="https://codecov.io/gh/projectcitybuild/web/"><img src="https://codecov.io/gh/projectcitybuild/web/branch/master/graph/badge.svg" alt="codecov"></a>
    <a href="https://dependabot.com"><img src="https://api.dependabot.com/badges/status?host=github&repo=projectcitybuild/web" alt="Dependabot Status"></a>
</p>

---

The official repository for [Project City Build](https://projectcitybuild.com)'s homepage and related web services.

### Stack
* Frameworks: Laravel 8, ReactJS 16
* Environment: Dockerised with Laravel Sail
* CI/CD: Github Actions, Codecov

All branches, commits and pull-requests are continuously tested

### Requirements
* Docker

We use Laravel Sail to create a dockerised development environment. You can also run it as a traditional application, you'll need:

* PHP 7.4
* MySQL/MariaDB
* Composer
* NPM

### Can I contribute?
Absolutely. Feel free to fork and send pull requests any time. We'd be thrilled to have some help.

# Contributing

You should read the [Laravel Sail](https://laravel.com/docs/8.x/sail) documentation first. If you're using Windows you have to run it through WSL2.

## First time setup

Run `make bootstrap`

## Development
Once *First time setup* is complete, you only need to run one command to boot up the environment:

`sail up -d` to start Sail

For front-end development, you'll want to run:

`sail npm watch` to start NPM build. This also starts BrowserSync on `http://localhost:3000`

You can enter the container at any time with `sail shell`

### Linter

We automatically run [PHP Insights](https://phpinsights.com/) on CI for linting.

You can run the linter locally with `sail php artisan insights`

If you want automatic fixing, you can run it with the `--fix` option

### Database
* If the database schema has changed, remember to run `sail artisan migrate` from inside the workspace container to ensure you always have the latest schema.

### Stripe Webhooks
Use [stripe-cli](https://stripe.com/docs/stripe-cli) to receive payment webhooks locally.

After installing, run `stripe listen --forward-to localhost/api/webhooks/stripe` to forward webhook events to the correct endpoint. Copy the code you're given into the `STRIPE_WEBHOOK_SECRET` env value.

## Testing
* Run `sail test` to run all unit/integration tests
* Enter the container with `sail shell` and run `phpstan -c phpstan.neon` to run PHP analysis
