<?php

use App\Http\Middleware\OidcAuthorizationAccess;
use App\Http\Middleware\OidcSharedState;
use App\Models\Account;

return [
    'issuer' => rtrim((string) env('OIDC_ISSUER', env('APP_URL')), '/'),
    'user_model' => Account::class,
    'configure_passport' => false,
    'ignore_passport_routes' => true,
    'freshness' => [
        'redis_connection' => env('OIDC_FRESHNESS_REDIS_CONNECTION', 'default'),
    ],
    'scopes' => [
        'openid' => [
            'description' => 'OpenID Connect authentication',
            'claims' => ['sub'],
        ],
        'profile' => [
            'description' => 'Access account profile information',
            'claims' => ['name', 'nickname', 'preferred_username'],
        ],
        'email' => [
            'description' => 'Access the account email address',
            'claims' => ['email'],
        ],
    ],
    'default_scopes' => ['openid'],
    'claims_resolver' => [],
    'default_claims_map' => [],
    'routes' => [
        'enabled' => true,
        'discovery_middleware' => [],
        'authorization_middleware' => [OidcSharedState::class, OidcAuthorizationAccess::class],
        'token_middleware' => [OidcSharedState::class],
        'userinfo_middleware' => ['auth:api'],
    ],
];
