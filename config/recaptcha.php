<?php

return [
    'enabled' => env('RECAPTCHA_ENABLED', false),

    'keys' => [
        'site' => env('RECAPTCHA_SITE_KEY'),
        'secret' => env('RECAPTCHA_SECRET_KEY'),
    ],
];
