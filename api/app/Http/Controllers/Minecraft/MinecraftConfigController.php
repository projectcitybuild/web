<?php

namespace App\Http\Controllers\Minecraft;

use App\Http\Controllers\APIController;
use Entities\Models\Eloquent\Group;
use Illuminate\Http\JsonResponse;

final class MinecraftConfigController extends APIController
{
    public function __invoke(): JsonResponse
    {
        $groups = Group::get();

        return response()->json([
           'groups' => $groups,
        ]);
    }
}
