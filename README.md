# Project City Build
[![License: MPL 2.0](https://img.shields.io/badge/License-MPL%202.0-brightgreen.svg)](https://opensource.org/licenses/MPL-2.0) [![Build Status](https://travis-ci.org/itsmyfirstday/ProjectCityBuild.svg?branch=v3.5)](https://travis-ci.org/itsmyfirstday/ProjectCityBuild)

Version 3.5 - basically just a supercharged, coat of fresh paint on the current design. Moving onwards from this point we will only be updating this version until it becomes version 4.0.

## What's different about this version?
Mostly behind the scenes stuff.
* Mobile responsive
* Powered by Laravel
* A few UI components are powered by React (eg. the ban list)
* Version 1 of the universal ban API
* Everything brought up to modern standards

### Stack
* Laravel 5.5
* ReactJS 15

### Requirements
The only requirement is **Docker** installed. If you do not wish to use Docker for local dev work, feel free to go about it in the usual way by working from the ``src`` directory. 

Without Docker, you will need to first manually install the below:

* PHP 7.1 or greater
* MySQL/MariaDB (either is fine)
* Composer
* NPM
* Sqlite (for integration tests)

### What still needs to be built before launch?
- [ ] Ban API
- [ ] Ban appeals
- [ ] Apply for staff
- [ ] Player id fetching after server status queries


### When will this release?
When the above checklist is complete.

### Can I contribute?
Absolutely. Feel free to send fork and send pull requests any time. I'd be thrilled to have some help since I'm very short on time.

# Contributing
## Set up
### With Docker
If you are using Docker (which is highly recommended for a plethora of reasons):
1. Copy the root **.env.example** file and rename it to **.env**. Fill in your desired new database details.
2. Copy the **src/.env.examples** file, rename it to **.env** and fill in at least your database connection details.
3. Run ``docker-compose up -d``

### Without Docker
1. Copy the **.env.examples** file, rename it to **.env** and fill in at least your database connection details.
2. Navigate into the **src** folder.
3. Download PHP dependencies by running `composer install`
4. Download JS dependencies by running `npm install` or `yarn install`
5. Create a new database
6. Set up the database by running `php artisan migrate --seed`


## Development
### Live editing
* From the **src** directory, run `npm run watch`. This will open up a Browsersync instance in your default browser with hot-reloading. 
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
* If the database schema changes, remember to run ``php artisan migrate`` from the **src** folder, to ensure you always have the latest schema.

## Note about forum data
Since we're still using SMF for the time being, the website will retrieve data from the SMF database on our production server. Since we obviously can't give out the server credentials for security reasons, you can mock the forum data instead by setting **DB_MOCK_FORUMS** to **true** in your .env file.