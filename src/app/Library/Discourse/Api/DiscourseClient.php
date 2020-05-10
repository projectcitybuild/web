<?php

namespace App\Library\Discourse\Api;

use GuzzleHttp\Client;

final class DiscourseClient extends Client
{
    private function getApiKey()
    {
        return config('discourse.api_key');
    }

    private function getApiUser()
    {
        return config('discourse.api_user');
    }

    public function __construct()
    {
        parent::__construct([
            'base_uri' => 'https://forums.projectcitybuild.com/',
            [
                'headers' => [
                    'Api-Key' => $this->getApiKey(),
                    'Api-User' => $this->getApiUser()
                ]
            ]
        ]);
    }
}
