<?php

return [
    'enabled' => env('RECAPTCHA_ENABLED', true),

    'keys' => [
        'site' => env('RECAPTCHA_SITE_KEY'),
        'secret' => env('RECAPTCHA_SECRET_KEY')
    ]
];
