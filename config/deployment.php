<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Deployment Branch
    |--------------------------------------------------------------------------
    |
    | The branch that will be checked out during deployment
    |
    */

    'branch' => env('DEPLOY_BRANCH', 'master'),

    /*
    |--------------------------------------------------------------------------
    | Deployment Key
    |--------------------------------------------------------------------------
    |
    | The key required to be passed in the request, to verify that the requester
    | is allowed to trigger an auto-deployment on our server
    |
    */

    'key' => env('DEPLOY_KEY', ''),

];
