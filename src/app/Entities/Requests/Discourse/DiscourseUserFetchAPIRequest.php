<?php

namespace App\Entities\Requests\Discourse;

use App\Library\APIClient\APIRequest;
use App\Library\APIClient\APIRequestMethod;

final class DiscourseUserFetchAPIRequest extends APIRequest
{
    public function path(): string 
    { 
        return "users/by-external/{$this->pcbId}.json"; 
    }

    public function method(): APIRequestMethod 
    { 
        return new APIRequestMethod(APIRequestMethod::GET); 
    }

    public function body(): ?array
    {
        return null;
    }

    private $pcbUserId;

    public function __construct(string $pcbUserId)
    {
        $this->pcbUserId = $pcbUserId;
    }
}