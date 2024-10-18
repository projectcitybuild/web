<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\ApiController;
use App\Models\ShowcaseWarp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MinecraftShowcaseWarpController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        return ShowcaseWarp::orderBy('name', 'asc')->get();
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
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
        ]);

        return ShowcaseWarp::create($request->all());
    }

    public function show(Request $request, string $name): JsonResponse
    {
        $warp = ShowcaseWarp::where('name', $name)->first();
        if ($warp === null) {
            abort(404);
        }
        return $warp;
    }

    public function update(Request $request, string $name): JsonResponse
    {
        $warp = ShowcaseWarp::where('name', $name)->first();
        if ($warp === null) {
            abort(404);
        }

        $request->validate([
            'location_world' => 'required|string',
            'location_x' => 'required|integer',
            'location_y' => 'required|integer',
            'location_z' => 'required|integer',
            'location_pitch' => 'required|numeric',
            'location_yaw' => 'required|numeric',
        ]);

        $warp->update($request->all());

        return $warp;
    }
}
