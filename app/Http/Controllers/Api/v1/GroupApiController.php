<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\ApiController;
use Entities\Models\Eloquent\Group;
use Entities\Resources\GroupResource;

final class GroupApiController extends ApiController
{
    public function getAll()
    {
        $groups = Group::get();

        return GroupResource::collection($groups);
    }
}
