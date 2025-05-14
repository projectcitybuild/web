<?php

namespace App\Http\Controllers\Api\v2\Minecraft\Player;

use App\Http\Controllers\ApiController;
use App\Models\MinecraftHome;
use Illuminate\Http\Request;

final class MinecraftPlayerHomeNameController extends ApiController
{
    public function index(Request $request)
    {
        return MinecraftHome::get(['id', 'name']);
    }
}
