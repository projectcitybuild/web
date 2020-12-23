<?php

namespace App\Library\Discourse\Api;

class DiscourseUserApi extends DiscourseAPIRequest
{
    /**
     * Finds a Discourse account that belongs to
     * the given PCB account id
     *
     * @return array
     */
    public function fetchUserByPcbId(int $pcbId): array
    {
        $response = $this->client->get('users/by-external/'.$pcbId.'.json');
        return json_decode($response->getBody(), true);
    }
}
