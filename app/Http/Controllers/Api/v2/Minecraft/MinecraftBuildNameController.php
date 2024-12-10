<?php

namespace App\Http\Controllers\Api\v2\Minecraft;

use App\Http\Controllers\ApiController;
use App\Models\MinecraftBuild;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class MinecraftBuildNameController extends ApiController
{
    public function index(Request $request)
    {
        return MinecraftBuild::get(['id', 'name']);
    }
}
