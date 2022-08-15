<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\ApiController;
use Entities\Models\Eloquent\ShowcaseWarp;
use Entities\Resources\ShowcaseWarpResource;
use Illuminate\Http\Request;

final class MinecraftShowcaseWarpController extends ApiController
{
    public function index(Request $request)
    {
        $warps = ShowcaseWarp::get();
        return ShowcaseWarpResource::collection($warps);
    }
}
