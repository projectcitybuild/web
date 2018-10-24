<?php
namespace Domains\Library\Discourse\Api;

class DiscourseGroupApi
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
    public function fetchGroupMembers(string $groupName, int $limit = 20) : array
    {
        $response = $this->client->get('groups/'.$groupName.'/members.json?limit='.$limit);
        $result = json_decode($response->getBody(), true);

        return $result;
    }
}
