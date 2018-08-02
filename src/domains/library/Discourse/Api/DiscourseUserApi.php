<?php
namespace Domains\Library\Discourse\Api;

class DiscourseUserApi
{

    /**
     * @var DiscourseClient
     */
    private $client;

    public function __construct(DiscourseClient $client)
    {
        $this->client = $client;
    }

    /**
     * Finds a Discourse account that belongs to
     * the given PCB account id
     *
     * @param integer $externalId
     * @return array
     */
    public function fetchUserByPcbId(int $externalId) : array
    {
        $response = $this->client->get('users/by-external/'.$externalId.'.json');
        $result = json_decode($response->getBody(), true);

        return $result;
    }
}
