# Project City Build
[![License: MPL 2.0](https://img.shields.io/badge/License-MPL%202.0-brightgreen.svg)](https://opensource.org/licenses/MPL-2.0) [![Build Status](https://travis-ci.org/itsmyfirstday/ProjectCityBuild.svg?branch=master)](https://travis-ci.org/itsmyfirstday/ProjectCityBuild)

Version 3.5 - basically a supercharged, coat of fresh paint on our old site design. Moving onwards from this point we will only be updating this version until it eventually becomes version 4.0.

## What's different about this version?
Mostly behind the scenes stuff.
* Screen responsive design
* Powered by Laravel
* A few UI components are powered by React (eg. the ban list)
* Version 1 of the universal ban API
* Everything brought up to modern standards
* Containerised via Docker

### Stack
* Frameworks
    * Laravel 5.6
    * ReactJS 15
* Languages
    * HTML, PHP, SASS, TypeScript
* Environment
    * Docker (and Docker-Compose)
* CI /  CD
    * Travis CI

### Requirements
The only requirement is **Docker** installed. If you do not wish to use Docker for local dev work, feel free to go about it in the usual way by working from the ``src`` directory. 

Without Docker, you will need to first manually install the below:

* PHP 7.1.3 or greater
* MySQL/MariaDB (either one is fine)
* Composer
* NPM

### What still needs to be built before launch?
See the **Projects** tab (1.0 Release Roadmap)


### When will this release?
When the Project checklist is complete.

### Can I contribute?
Absolutely. Feel free to fork or send pull requests any time. I'd be thrilled to have some help.

# Contributing
## Set up
### With Docker
If you are using Docker (which I highly recommend):
1. Copy the root **.env.example** file and rename it to **.env**. Fill in your desired new database details.
2. Copy the **src/.env.examples** file, rename it to **src/.env** and fill in at least your database connection details, matching the first step.
3. Run ``docker-compose up -d`` to boot up the stack
4. Enter the php-fpm container `php exec -it <php-fpm id> sh`
5. Generate a Laravel application key `php artisan key:generate`
6. Download PHP dependencies by running `composer install`
7. Download JS dependencies by running `npm install`
8. Set up the database by running `php artisan migrate --seed`

### Without Docker
1. Copy the **.env.examples** file, rename it to **.env** and fill in at least your database connection details.
2. Navigate into the **src** folder.
3. Generate a Laravel application key `php artisan key:generate`
4. Download PHP dependencies by running `composer install`
5. Download JS dependencies by running `npm install`
6. Create a new database
7. Set up the database by running `php artisan migrate --seed`


## Development
### Live editing
* From the **src** directory, run `npm run watch`. This will open up a BrowserSync instance in your default browser with hot-reloading. 
* If you are not using Docker, you will need to change the BrowserSync setting in **webpack.mix.js** to ``.browserSync();``

### Testing
* From the **src** directory, run `phpunit`

### Note when using Docker:
Since none of the dependencies are actually installed on your host computer, you will need to enter the **php-fpm** container before running any of the above.
* ``docker ps`` will print a table of all containers running. Look for the php-fpm container id.
* ``docker exec -it <php-fpm container id> sh`` will enter the container in interactive mode.
* Alternatively you can run commands from the host. For example ``docker exec <php-fpm container id> phpunit``
* When running inside a Docker container, ``npm run watch`` will not automatically open a browser for you. Simply navigate to **192.168.99.100:3000** in the browser of your host (or whatever your docker-machine IP is).

### Database
* If the database schema changes, remember to run ``php artisan migrate`` from the **src** folder to ensure you always have the latest schema.

## Note about forum data
Since we're still using SMF for the time being, the website will retrieve data from the SMF database on our production server. Since we obviously can't give out the server credentials for security reasons, you can mock the forum data instead by setting **DB_MOCK_FORUMS** to **true** in your .env file.