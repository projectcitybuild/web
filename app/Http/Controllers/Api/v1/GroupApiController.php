<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\ApiController;
use App\Http\Resources\GroupResource;
use App\Models\Group;

final class GroupApiController extends ApiController
{
    public function getAll()
    {
        return GroupResource::collection(Group::get());
    }
}
