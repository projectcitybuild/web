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
**Docker** is an optional requirement. If you do not wish to use Docker for local dev work, feel free to go about it in the usual way by working from the `src` directory. 

Without Docker, you will need to manually install:

* PHP 7.1.3 or greater
* MySQL/MariaDB
* Composer
* NPM

### Can I contribute?
Absolutely. Feel free to fork and send pull requests any time. I'd be thrilled to have some help.

# Contributing
### Set up
If you are using Docker (which I highly recommend):
1. Copy the root `.env.example` file and rename it to `.env`. Fill in your desired new database details.
2. Copy the `src/.env.examples` file, rename it to `src/.env` and fill in at least your database connection details, matching the first step.
3. Run ``docker-compose up -d`` to boot up the stack
4. Enter the php-fpm container `docker exec -it <php-fpm id> sh` (use `docker ps` to find the id)
5. Download PHP dependencies by running `composer install`
6. Download JS dependencies by running `npm install`
7. Generate a Laravel application key `php artisan key:generate`
8. Set up the database by running `php artisan migrate --seed`

### Live editing
* From the **src** directory, run `npm run watch`. This will open up a BrowserSync instance in your default browser with hot-reloading. 
* If your docker-machine IP is different to the default (192.168.99.100), you will need to change the BrowserSync proxy setting in `webpack.mix.js`.

### Testing
* Run `phpunit` inside the php-fpm docker container

### Note when using Docker:
Because none of the dependencies are actually installed on your computer, you will need to enter the **php-fpm** container to run any Composer, NPM or PHP commands.
* ``docker ps`` will print a table of all containers running. Look for the php-fpm container id.
* ``docker exec -it <php-fpm container id> sh`` will enter the container in interactive mode.
* Alternatively you can run commands from the host. For example ``docker exec <php-fpm container id> phpunit``
* When running inside a Docker container, ``npm run watch`` will not automatically open a browser for you. Navigate to **192.168.99.100:3000** (or whatever your docker-machine IP is) in the browser of your computer.

### Database
* If the database schema changes, remember to run ``php artisan migrate`` from the **src** folder to ensure you always have the latest schema.
