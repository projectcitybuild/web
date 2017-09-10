<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cookie name
    |--------------------------------------------------------------------------
    |
    | Name of the cookie created by SMF that the service will intercept
    | to determine if the current user is logged-in or not.
    |
    */

    'cookie_name' => 'PCBSMF',

    /*
    |--------------------------------------------------------------------------
    | Staff Group IDs
    |--------------------------------------------------------------------------
    |
    | Array of membergroup ids that are considered to be staff and will cause
    | IsStaff() to return TRUE.
    |
    */

    'staff_group_ids' => [1, 2, 3, 13, 14],

    /*
    |--------------------------------------------------------------------------
    | 'Player Reports' board ID
    |--------------------------------------------------------------------------
    |
    | ID of the board where Player Reports get posted
    |
    */

    'board_id_reports' => 16,

    /*
    |--------------------------------------------------------------------------
    | 'Ban Appeals' board ID
    |--------------------------------------------------------------------------
    |
    | ID of the board where Ban Appeals get posted
    |
    */

    'board_id_appeals' => 4,

    /*
    |--------------------------------------------------------------------------
    | Staff Application board ID
    |--------------------------------------------------------------------------
    |
    | ID of the board where Staff Applications get posted
    |
    */

    'board_id_staff_apps' => 21,

    /*
    |--------------------------------------------------------------------------
    | Recycling bin board ID
    |--------------------------------------------------------------------------
    |
    | ID of the board where deleted posts are sent
    |
    */

    'board_id_bin' => 19,

    /*
    |--------------------------------------------------------------------------
    | Administrator group ID
    |--------------------------------------------------------------------------
    |
    | ID of the administrator group
    |
    */

    'group_id_admin' => 1,

    /*
    |--------------------------------------------------------------------------
    | Donator group ID
    |--------------------------------------------------------------------------
    |
    | ID of the donator group
    |
    */

    'group_id_donator' => 11,

];
