<?php

namespace App\Http\Controllers\Api\v1;

use App\Entities\Models\Eloquent\Group;
use App\Entities\Resources\GroupResource;
use App\Http\ApiController;

final class GroupApiController extends ApiController
{
    public function getAll()
    {
        $groups = Group::get();

        return GroupResource::collection($groups);
    }
}
