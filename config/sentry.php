<?php

return [
    'dsn' => env('SENTRY_DSN'),

    // capture release as git sha
    'release' => \Library\Environment\Environment::isProduction() ? trim(exec('git log --pretty="%h" -n1 HEAD')) : 'NON_PRODUCTION',

    // Capture bindings on SQL queries
    'breadcrumbs.sql_bindings' => true,
];
