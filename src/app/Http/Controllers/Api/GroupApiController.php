<?php

namespace App\Http\Controllers\Api;

use App\Entities\Groups\Models\Group;
use App\Http\ApiController;
use App\Entities\Groups\Resources\GroupResource;

final class GroupApiController extends ApiController
{
    public function getAll()
    {
        $groups = Group::get();
        $response = GroupResource::collection($groups);

        return $response;
    }
}
