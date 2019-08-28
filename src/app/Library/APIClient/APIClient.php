<?php

namespace App\Library\APIClient;

use GuzzleHttp\Client;

final class APIClient
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    private function getApiKey()
    {
        return env('DISCOURSE_API_KEY');
    }

    private function getApiUser()
    {
        return env('DISCOURSE_API_USER');
    }

    public function request(APIRequest $request) : array
    {
        // TODO: Move base URL to somewhere more appropriate
        $endpoint = 'https://forums.projectcitybuild.com/'.$request->path();

        $response = null;
        $query = [
            'api_key'       => $this->getApiKey(),
            'api_username'  => $this->getApiUser(),
        ];

        switch ($request->method()->valueOf()) {
            case APIRequestMethod::GET:
                $query = array_merge($query, $request->body());
                $response = $this->client->get($endpoint, [
                    'query' => $query
                ]);

            case APIRequestMethod::POST:
                $response = $this->client->post($endpoint, [
                    'query' => $query,
                    'form_params' => $request->body(),
                ]);

            default:
                throw new \Exception('Unhandled HTTP method');
        }

        return json_decode($response->getBody(), true);
    }
}