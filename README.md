# Project City Build
[![License: MPL 2.0](https://img.shields.io/badge/License-MPL%202.0-brightgreen.svg)](https://opensource.org/licenses/MPL-2.0) [![Build Status](https://travis-ci.org/itsmyfirstday/ProjectCityBuild.svg?branch=v3.5)](https://travis-ci.org/itsmyfirstday/ProjectCityBuild)

Version 3.5 - basically just a supercharged, coat of fresh paint on the current design.

## What's different about this version?
Mostly behind the scenes stuff.
* Mobile responsive
* Powered by Laravel
* A few UI components are powered by React (eg. the ban list)
* Version 1 of the universal ban API

### Stack
* Laravel 5.5
* ReactJS 15

### Requirements
* PHP 7.0 or greater
* MySQL/MariaDB
* Sqlite (for integration tests)
* Composer and NPM

### What still needs to be built before launch?
- [x] Server querying
- [x] Server Feed
- [x] 'Recent Posts' sidebar integration with SMF
- [ ] Ban API
- [ ] Ban appeals
- [x] Ban list
- [x] Donations
- [ ] Apply for staff


### When will this release?
Tentatively aiming for the end of this month (October 2017).

### Can I contribute?
Absolutely. Feel free to send pull requests any time. I'd be thrilled to have some help since I'm very short on time.

# Contributing
### Set up
1. Download PHP dependencies using your CLI of choice
`composer install`
2. Download JS dependencies using your CLI of choice
`npm install` or `yarn install`
3. Create a new database
4. Copy the **.env.examples** file and rename it to **.env**. Fill in at least your database connection details.
`cp .env.examples .env`
5. Run the below command in your CLI of choice to set up the tables and data.
`php artisan migrate --seed`

### Live viewing
1. Via your CLI, browse to the repository root
2. `npm run watch` This will open up a Browserify instance in your default browser with hot-reloading.

### Testing
1. Via your CLI, browse to the repository root
2. `phpunit`