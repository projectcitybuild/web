<?php

namespace App\Http\Controllers\Api\v2\Minecraft\Warps;

use App\Http\Controllers\ApiController;
use App\Models\MinecraftWarp;
use Illuminate\Http\Request;

final class MinecraftWarpNameController extends ApiController
{
    public function index(Request $request)
    {
        return MinecraftWarp::get(['id', 'name']);
    }
}
