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
Absolutely. Feel free to fork and send pull requests any time. I'd be thrilled to have some help.

# Contributing

You should read the [Laravel Sail](https://laravel.com/docs/8.x/sail) documentation first. If you're using Windows you have to run it through WSL2.

For brevity this readme assumes you've aliased `vendor/bin/sail` to `sail`. If not, you need to write it out in full each time.

## First time setup
This repository uses *Laravel Sail* as a local development environment.

1. Run `cp src/.env.example src/.env`, then edit the file as appropriate (see below)
2. Install the composer dependencies using the helper container
```shell
docker run --rm \
    -v $(pwd):/opt \
    -w /opt \
    laravelsail/php74-composer:latest \
    composer install
```

3. Start Laravel Sail with `sail up -d`
4. `sail artisan migrate --seed`
5. Run `sail npm install`
6. Run `sail npm watch`

## Development
Once *First time setup* is complete, you only need to run one command to boot up the environment:

1. `sail up -d` to start Sail
2. `sail npm watch` to start NPM build. This also starts BrowserSync on `http://localhost:3000`

You can enter the workspace with `sail shell`

#### Linter

We use [PHP Insights](https://phpinsights.com/) for linting.

You can run the linter locally with `sail php artisan insights`

If you want automatic fixing, you can run it with the `--fix` option

#### Database
If the database schema has changed, remember to run `sail artisan migrate` from inside the workspace container to ensure you always have the latest schema.

#### S3 Bucket
Backups go to an S3 bucket specified in the `backup` disk. To run this functionality in development, you need to configure a valid bucket. To avoid having to use a real one:

1. Install Minio with [Takeout](https://github.com/tighten/takeout), accepting the defaults
2. Go to `http://localhost:9000`, using the credentials minioadmin/minioadmin
3. Make a bucket called `pcb-backup`
4. Put this in your `.env`:

```dotenv
AWS_ACCESS_KEY_ID=minioadmin
AWS_SECRET_ACCESS_KEY=minioadmin
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=pcb-backup
AWS_ENDPOINT="http://minio:9000"
AWS_URL="http://minio:9000"
```

The Sail container's networking has already been configured to connect to Takeout's networking, so the Minio container is accessible at the hostname `minio`.

#### Stripe Webhooks
Use [stripe-cli](https://stripe.com/docs/stripe-cli) to receive payment webhooks locally.

After installing, run `stripe listen --forward-to localhost/api/webhooks/stripe` to forward webhook events to the correct endpoint. Copy the code you're given into the `STRIPE_WEBHOOK_SECRET` env value.

## Testing
Inside the workspace container:
* Run `sail test` to run all unit/integration tests
* Enter the container with `sail shell` and run `phpstan -c phpstan.neon` to run PHP analysis
