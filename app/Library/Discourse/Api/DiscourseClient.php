<?php

namespace App\Library\Discourse\Api;

use GuzzleHttp\Client;

final class DiscourseClient extends Client
{

    public function __construct()
    {
        parent::__construct([
            'base_uri' => config('discourse.base_url'),
            'headers' => [
                'Api-Key' => $this->getApiKey(),
                'Api-Username' => $this->getApiUser(),
            ],
        ]);
    }
    private function getApiKey()
    {
        return config('discourse.api_key');
    }

    private function getApiUser()
    {
        return config('discourse.api_user');
    }
}
