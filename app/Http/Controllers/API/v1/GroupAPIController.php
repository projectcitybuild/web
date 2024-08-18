<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\APIController;
use App\Models\Group;
use Entities\Resources\GroupResource;

final class GroupAPIController extends APIController
{
    public function getAll()
    {
        $groups = Group::get();

        return GroupResource::collection($groups);
    }
}
