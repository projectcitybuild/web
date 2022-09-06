<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\APIController;
use Entities\Models\Eloquent\ShowcaseWarp;
use Entities\Resources\ShowcaseWarpResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class MinecraftShowcaseWarpController extends APIController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $warps = ShowcaseWarp::get();
        return ShowcaseWarpResource::collection($warps);
    }

    public function store(Request $request): ShowcaseWarpResource
    {
        $this->validateRequest(
            requestData: $request->all(),
            rules: [
                'name' => 'required|string|unique',
                'title' => 'string',
                'description' => 'string',
                'creators' => 'string',
                'location_world' => 'required|string',
                'location_x' => 'required|integer',
                'location_y' => 'required|integer',
                'location_z' => 'required|integer',
                'location_pitch' => 'required|numeric',
                'location_yaw' => 'required|numeric',
                'built_at' => 'integer',
            ],
        );

        $warp = ShowcaseWarp::create($request->all());

        return new ShowcaseWarpResource($warp);
    }
}
