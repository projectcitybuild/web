<p align="center">
    <img src="https://projectcitybuild.com/assets/images/logo.png" alt="Project City Build"/>
</p>

<p align="center">
    <a href="https://opensource.org/licenses/MPL-2.0"><img src="https://img.shields.io/badge/License-MPL%202.0-brightgreen.svg" alt="License: MPL 2.0"></a>
    <a href="https://travis-ci.org/andyksaw/ProjectCityBuild"><img src="https://travis-ci.org/andyksaw/ProjectCityBuild.svg?branch=master" alt="Build status"></a>
    <a href="https://codecov.io/gh/andyksaw/ProjectCityBuild"><img src="https://codecov.io/gh/andyksaw/ProjectCityBuild/branch/master/graph/badge.svg" alt="codecov"></a>
    <a href="https://dependabot.com"><img src="https://api.dependabot.com/badges/status?host=github&repo=andyksaw/ProjectCityBuild" alt="Dependabot Status"></a>
</p>

---

The official repository for [Project City Build](https://projectcitybuild.com)'s homepage and related web services.

### Stack
* Frameworks: Laravel 5.8, ReactJS 16
* Environment: Docker (and Docker-Compose)
* CI/CD: Travis CI, Codecov

All branches, commits and pull-requests are continuously tested

### Requirements
* Docker

If you do not wish to use Docker for local dev work, feel free to go about it in the usual way by working from the `src` directory. However, you will need to install and setup all requirements manually:

* PHP 7.2+
* MySQL/MariaDB
* Composer
* NPM

### Can I contribute?
Absolutely. Feel free to fork and send pull requests any time. I'd be thrilled to have some help.

# Contributing
## First time setup
This repository uses *laradock* as a local development environment.

1. Enter the `laradock` folder
2. Build the docker container: `docker-compose up -d nginx mariadb redis`
3. Run `cp src/.env.example src/.env`, then edit the file as appropriate (see below)
4. Enter the main workspace container: `docker-compose exec workspace bash`
    1. Install PHP dependencies: `composer install`
    2. Install JS dependencies: `npm install` (see below before running this)
    3. Generate private key: `php artisan key:generate`
    4. Create and seed database: `php artisan migrate --seed`
    5. Build JS/CSS assets: `npm run dev`

You should now be able to see the site at [http://localhost](http://localhost). If that doesn't work, also try [http://192.168.99.100](http://192.168.99.100).


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
Once *First time setup* is complete, you only need to run one command to boot up the environment:
```
docker-compose up -d nginx mariadb redis
```

To enter the workspace at anytime use `docker-compose exec workspace bash`


#### Live editing (Browsersync)
* From inside the container, run `npm run watch`. Because we're running in headless-mode, no browser will automatically open.
* Open `http://localhost:3000` in your browser. Any HTML, JS, CSS changes will automatically appear without refreshing. 

#### Database
* If the database schema has changed, remember to run `php artisan migrate` from inside the workspace container to ensure you always have the latest schema.

## Testing
Inside the workspace container:
* Run `phpunit` to run all unit/integration tests
* Run `phpstan -c phpstan.neon` to run PHP analysis

## Troubleshooting Laradock
Docker might not go smoothly for Windows users. If you're having trouble booting up a MariaDB instance, you may need to use a MySQL container instead.
In this case the boot command would be `docker-compose up -d nginx mysql redis`.
The `.env` file will also need to be updated to point at the correct container: `DB_HOST=mysql`
