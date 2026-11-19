<?php

$appUrl = (string) env('APP_URL', 'http://localhost');
$originParts = parse_url($appUrl);

if (! isset($originParts['host'])) {
    $originParts = parse_url('//'.$appUrl) ?: [];
}

$appOrigin = ($originParts['scheme'] ?? 'http').'://'.($originParts['host'] ?? 'localhost');

if (isset($originParts['port'])) {
    $appOrigin .= ':'.$originParts['port'];
}

return [
    'paths' => ['v3/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [$appOrigin],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
