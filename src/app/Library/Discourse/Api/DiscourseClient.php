<?php

namespace App\Library\Discourse\Api;

use GuzzleHttp\Client;

/**
 * @deprecated 1.11.0 Use APIClientProvider instead
 */
final class DiscourseClient extends Client
{
    public function __construct()
    {
        parent::__construct([
            'base_uri' => 'https://forums.projectcitybuild.com/',
        ]);
    }
}
