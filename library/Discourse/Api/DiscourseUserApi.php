<?php

namespace Library\Discourse\Api;

use GuzzleHttp\Client;

class DiscourseUserApi
{
    private Client $client;

    public function __construct(private DiscourseClientFactory $clientFactory)
    {
        $this->client = $this->clientFactory->make();
    }

    /**
     * Finds a Discourse account that belongs to
     * the given PCB account id.
     */
    public function fetchUserByPcbId(int $pcbId): array
    {
        $response = $this->client->get('users/by-external/'.$pcbId.'.json');

        return json_decode($response->getBody(), associative: true);
    }
}
