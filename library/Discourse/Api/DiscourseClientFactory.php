<?php

namespace Library\Discourse\Api;

use GuzzleHttp\Client;
use function config;

final class DiscourseClientFactory
{
    public function make(): Client
    {
        return new Client([
            'base_uri' => config('discourse.base_url'),
            'headers' => [
                'Api-Key' => config('discourse.api_key'),
                'Api-Username' => config('discourse.api_user'),
            ],
        ]);
    }
}
