<?php

namespace App\Http\Controllers\Api\v3\Warps;

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
