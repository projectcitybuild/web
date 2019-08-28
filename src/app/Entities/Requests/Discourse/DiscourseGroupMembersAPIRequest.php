<?php

namespace App\Entities\Requests\Discourse;

use App\Library\APIClient\APIRequest;
use App\Library\APIClient\APIRequestMethod;

/**
 * Fetches all members that belong to the given group name
 */
final class DiscourseGroupMembersAPIRequest extends APIRequest
{
    public function path(): string 
    { 
        return "groups/{$this->groupName}/members.json?limit={$this->limit}";
    }

    public function method(): APIRequestMethod 
    { 
        return new APIRequestMethod(APIRequestMethod::GET); 
    }

    public function body(): ?array
    {
        return null;
    }

    private $groupName;
    private $limit;

    public function __construct(string $groupName, int $limit = 20)
    {
        $this->groupName = $groupName;
        $this->limit = $limit;
    }
}