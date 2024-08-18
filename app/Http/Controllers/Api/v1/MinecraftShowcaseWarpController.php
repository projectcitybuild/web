<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\ApiController;
use App\Http\Resources\ShowcaseWarpResource;
use App\Models\ShowcaseWarp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MinecraftShowcaseWarpController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $warps = ShowcaseWarp::orderBy('name', 'asc')->get();

        return response()->json([
            'data' => ShowcaseWarpResource::collection($warps),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->validateRequest(
            requestData: $request->all(),
            rules: [
                'name' => 'required|string|unique:showcase_warps,name',
                'title' => 'nullable|string',
                'description' => 'nullable|string',
                'creators' => 'nullable|string',
                'location_world' => 'required|string',
                'location_x' => 'required|integer',
                'location_y' => 'required|integer',
                'location_z' => 'required|integer',
                'location_pitch' => 'required|numeric',
                'location_yaw' => 'required|numeric',
                'built_at' => 'nullable|integer',
            ],
        );

        $warp = ShowcaseWarp::create($request->all());

        return response()->json([
            'data' => ShowcaseWarpResource::make($warp),
        ]);
    }

    public function show(Request $request, string $name): JsonResponse
    {
        $warp = ShowcaseWarp::where('name', $name)->first();
        if ($warp === null) {
            abort(404);
        }

        return response()->json([
            'data' => is_null($warp) ? null : ShowcaseWarpResource::make($warp),
        ]);
    }

    public function update(Request $request, string $name): JsonResponse
    {
        $warp = ShowcaseWarp::where('name', $name)->first();
        if ($warp === null) {
            abort(404);
        }

        $this->validateRequest(
            requestData: $request->all(),
            rules: [
                'location_world' => 'required|string',
                'location_x' => 'required|integer',
                'location_y' => 'required|integer',
                'location_z' => 'required|integer',
                'location_pitch' => 'required|numeric',
                'location_yaw' => 'required|numeric',
            ],
        );

        $warp->update($request->all());

        return response()->json([
            'data' => ShowcaseWarpResource::make($warp),
        ]);
    }
}
