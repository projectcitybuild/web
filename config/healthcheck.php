<?php

return [
    'checks' => [
        'scheduler' => env('HEALTHCHECK_URL_SCHEDULER'),
        'queue' => env('HEALTHCHECK_URL_QUEUE'),
        'mail' => env('HEALTHCHECK_URL_MAIL'),
    ],

    'mail' => [
        'enabled' => env('HEALTHCHECK_MAIL_ENABLED', false),
        'to' => env('HEALTHCHECK_MAIL_TO'),
    ],
];
