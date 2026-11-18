<?php

namespace App\Http\Controllers\Api\v3\Warps;

use App\Http\Controllers\ApiController;
use App\Models\MinecraftWarp;
use Illuminate\Http\Request;

final class MinecraftWarpNameController extends ApiController
{
    /**
     * @response list<array{id: int, name: string}>
     */
    public function index(Request $request)
    {
        return MinecraftWarp::get(['id', 'name']);
    }
}
