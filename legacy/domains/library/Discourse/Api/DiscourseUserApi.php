<?php

namespace Domains\Library\Discourse\Api;

class DiscourseUserApi extends DiscourseAPIRequest
{
    /**
     * Finds a Discourse account that belongs to
     * the given PCB account id
     *
     * @param integer $pcbId
     * @return array
     */
    public function fetchUserByPcbId(int $pcbId) : array
    {
        $response = $this->client->get('users/by-external/'.$pcbId.'.json');
        $result = json_decode($response->getBody(), true);

        return $result;
    }
}
