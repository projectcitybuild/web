<?php

namespace App\Http\Controllers\Minecraft;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\JsonResponse;

final class MinecraftConfigController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $groups = Group::get();

        return response()->json([
           'groups' => $groups,
        ]);
    }
}
