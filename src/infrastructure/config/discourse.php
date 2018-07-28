<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Payload signing
    |--------------------------------------------------------------------------
    |
    | When enabled, verifies that the login route contains a valid
    | nonce and payload in the URL
    |
    */
    'signing_enabled'   => env('DISCOURSE_ENABLE_PAYLOAD_SIGNING', true),

];
