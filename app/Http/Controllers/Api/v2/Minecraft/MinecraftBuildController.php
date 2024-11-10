<?php

namespace App\Http\Controllers\Api\v2\Minecraft;

use App\Http\Controllers\ApiController;
use App\Models\MinecraftBuild;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class MinecraftBuildController extends ApiController
{
    public function index(Request $request)
    {
        $request->validate([
           'page_size' => 'integer|gt:0',
        ]);

        $defaultSize = 25;
        $pageSize = min($defaultSize, $request->get('page_size', $defaultSize));

        return MinecraftBuild::orderBy('votes', 'desc')
            ->paginate($pageSize);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', Rule::unique(MinecraftBuild::tableName())],
            'world' => 'required|string',
            'x' => 'required|numeric',
            'y' => 'required|numeric',
            'z' => 'required|numeric',
            'pitch' => 'required|numeric',
            'yaw' => 'required|numeric',
        ]);

        return MinecraftBuild::create($request->all());
    }

    public function update(Request $request, MinecraftBuild $build)
    {
        $request->validate([
            'name' => ['required', 'string', 'alpha_dash', Rule::unique('minecraft_warps')->ignore($build)],
            'world' => 'required|string',
            'x' => 'required|numeric',
            'y' => 'required|numeric',
            'z' => 'required|numeric',
            'pitch' => 'required|numeric',
            'yaw' => 'required|numeric',
        ]);

        $build->update($request->all());

        return $build;
    }

    public function destroy(Request $request, MinecraftBuild $warp)
    {
        $warp->delete();

        return response()->json();
    }
}
