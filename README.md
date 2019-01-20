![PCB](https://projectcitybuild.com/assets/images/logo.png)

[![License: MPL 2.0](https://img.shields.io/badge/License-MPL%202.0-brightgreen.svg)](https://opensource.org/licenses/MPL-2.0) [![Build Status](https://travis-ci.org/andyksaw/ProjectCityBuild.svg?branch=master)](https://travis-ci.org/andyksaw/ProjectCityBuild)
[![codecov](https://codecov.io/gh/andyksaw/ProjectCityBuild/branch/master/graph/badge.svg)](https://codecov.io/gh/andyksaw/ProjectCityBuild)

---

The official repository for [Project City Build](https://projectcitybuild.com)'s homepage and related web services.

### Stack
* Frameworks: Laravel 5.6, ReactJS 16
* Environment: Docker (and Docker-Compose)
* CI/CD: Travis CI, Codecov

All branches, commits and pull-requests are continuously tested

### Requirements
* Docker

If you do not wish to use Docker for local dev work, feel free to go about it in the usual way by working from the `src` directory. However, you will need to install and setup all requirements manually:

* PHP 7.2
* MySQL/MariaDB
* Composer
* NPM

### Can I contribute?
Absolutely. Feel free to fork and send pull requests any time. I'd be thrilled to have some help.

# Contributing
## First time setup
This repository uses *laradock* as a local development environment.

1. Setup docker container: `docker-compose up -d nginx mariadb redis`
2. Enter the main workspace container: `docker-compose exec workspace bash`
    1. Run `cp .env.example .env` and fill out the details (make sure `DB_HOST=mysql`)
    2. Install PHP dependencies: `composer install`
    3. Install JS dependencies: `npm installl`
    4. Generate private key: `php artisan key:generate`
    5. Create and seed database: `php artisan migrate --seed`

## Development
#### Live editing
* From inside the container, run `npm run watch`. This will open up a BrowserSync instance in your default browser with hot-reloading. 
* If your docker-machine IP is different to the default (192.168.99.100), you will need to change the BrowserSync proxy setting in `webpack.mix.js`.

#### Testing
* Run `phpunit` inside the workspace container

#### Database
* If the database schema has changed, remember to run `php artisan migrate` from inside the worksapce container to ensure you always have the latest schema.
