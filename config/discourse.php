<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Integration
    |--------------------------------------------------------------------------
    |
    | If false, all integration with Discourse will be disabled (no API hits,
    | no Discourse login, etc)
    |
    */
    'enabled' => env(key: 'DISCOURSE_INTEGRATION_ENABLED', default: false),

    /*
    |--------------------------------------------------------------------------
    | Payload signing
    |--------------------------------------------------------------------------
    |
    | When enabled, verifies that the login route contains a valid
    | nonce and payload in the URL
    |
    */
    'signing_enabled' => env('DISCOURSE_ENABLE_PAYLOAD_SIGNING', true),

    /*
    |--------------------------------------------------------------------------
    | Discourse SSO key
    |--------------------------------------------------------------------------
    |
    | Key for signing and verifying payloads from Discourse
    |
    */
    'sso_secret' => env('DISCOURSE_SSO_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Discourse SSO endpoint
    |--------------------------------------------------------------------------
    |
    | The URL of the Discourse instance with trailing slash
    |
    */
    'base_url' => env('DISCOURSE_BASE_URL'),

    'api_key' => env('DISCOURSE_API_KEY'),

    'api_user' => env('DISCOURSE_API_USER'),

];
