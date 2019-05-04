<?php

namespace App\Library\Discourse\Api;

/**
 * @deprecated 1.11.0 Use APIClientProvider instead
 */
class DiscourseUserApi extends DiscourseAPIRequest
{
    /**
     * Finds a Discourse account that belongs to
     * the given PCB account id
     *
     * @param integer $pcbId
     * @return array
     * @deprecated 1.11.0
     */
    public function fetchUserByPcbId(int $pcbId) : array
    {
        $response = $this->client->get('users/by-external/'.$pcbId.'.json');
        $result = json_decode($response->getBody(), true);

        return $result;
    }
}
