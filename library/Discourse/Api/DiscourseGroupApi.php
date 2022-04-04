<?php

namespace Library\Discourse\Api;

use GuzzleHttp\Client;

class DiscourseGroupApi
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
    public function fetchGroupMembers(string $groupName, int $limit = 20): array
    {
        $response = $this->client->get('groups/'.$groupName.'/members.json?limit='.$limit);

        return json_decode($response->getBody(), true);
    }
}
