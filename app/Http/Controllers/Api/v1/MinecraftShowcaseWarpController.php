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

    public function store(Request $request)
    {
        $this->validateRequest(
            requestData: $request->all(),
            rules: [
                'name' => 'required|string',
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

        ShowcaseWarp::create($request->all());

        return response()->json(null);
    }
}
