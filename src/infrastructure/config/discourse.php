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

    /*
    |--------------------------------------------------------------------------
    | Discourse SSO key
    |--------------------------------------------------------------------------
    |
    | Key for signing and verifying payloads from Discourse
    |
    */
    'sso_secret' => env('DISCOURSE_SSO_SECRET')

];
