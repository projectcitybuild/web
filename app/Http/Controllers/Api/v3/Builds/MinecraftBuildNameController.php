<?php

namespace App\Http\Controllers\Api\v3\Builds;

use App\Http\Controllers\ApiController;
use App\Models\MinecraftBuild;
use Illuminate\Http\Request;

final class MinecraftBuildNameController extends ApiController
{
    public function index(Request $request)
    {
        return MinecraftBuild::get(['id', 'name']);
    }
}
