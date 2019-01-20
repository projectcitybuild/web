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

1. Build the docker container: `docker-compose up -d nginx mariadb`
2. Run `cp src/.env.example src/.env`, then edit the file as appropriate (see below)
3. Enter the main workspace container: `docker-compose exec workspace bash`
    1. Install PHP dependencies: `composer install`
    2. Install JS dependencies: `npm installl`
    3. Generate private key: `php artisan key:generate`
    4. Create and seed database: `php artisan migrate --seed`
    5. Build JS/CSS assets: `npm run dev`

You should now be able to see the site at [http://localhost](http://localhost)


#### NPM Install
`Python2.7` is required by one of our dependencies. Unfortunately the workspace container does not have this pre-installed, so this step is required before running `npm install`:

```
apt-get update -yqq && apt-get install -y python2.7
npm config set python /usr/bin/python2.7
```

#### Local Environment File (.env)
Database config:
```
DB_CONNECTION=mysql
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=default
DB_USERNAME=default
DB_PASSWORD=secret
```

## Development
#### Live editing
* From inside the container, run `npm run watch`. This will open up a BrowserSync instance in your default browser with hot-reloading. 
* If your docker-machine IP is different to the default (192.168.99.100), you will need to change the BrowserSync proxy setting in `webpack.mix.js`.

#### Database
* If the database schema has changed, remember to run `php artisan migrate` from inside the worksapce container to ensure you always have the latest schema.

## Testing
* Run `phpunit` inside the workspace container