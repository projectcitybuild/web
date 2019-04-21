<?php

namespace Domains\Library\Discourse\Api;

use GuzzleHttp\Client;

final class DiscourseClient extends Client
{
    public function __construct()
    {
        parent::__construct([
            'base_uri' => 'https://forums.projectcitybuild.com/',
        ]);
    }
}
