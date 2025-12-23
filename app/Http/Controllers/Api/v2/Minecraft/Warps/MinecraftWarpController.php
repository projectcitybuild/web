<?php

namespace App\Http\Controllers\Api\v2\Minecraft\Warps;

use App\Core\Domains\MinecraftCoordinate\ValidatesCoordinates;
use App\Core\Domains\Pagination\HasPaginatedApi;
use App\Http\Controllers\ApiController;
use App\Models\MinecraftWarp;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class MinecraftWarpController extends ApiController
{
    use HasPaginatedApi;
    use ValidatesCoordinates;

    public function index(Request $request)
    {
        $validated = $request->validate([
            ...$this->paginationRules,
        ]);

        $pageSize = $this->pageSize($validated);

        return MinecraftWarp::orderBy('name', 'asc')
            ->paginate($pageSize);
    }

    public function bulk(Request $request)
    {
        return MinecraftWarp::orderBy('name', 'asc')->get();
    }

    public function show(Request $request, MinecraftWarp $warp)
    {
        return $warp;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'alpha_dash', Rule::unique(MinecraftWarp::tableName())],
            ...$this->coordinateRules,
        ]);

        return MinecraftWarp::create($request->all());
    }

    public function update(Request $request, MinecraftWarp $warp)
    {
        $request->validate([
            'name' => ['required', 'string', 'alpha_dash', Rule::unique(MinecraftWarp::tableName())->ignore($warp)],
            ...$this->coordinateRules,
        ]);

        $warp->update($request->all());

        return $warp;
    }

    public function destroy(Request $request, MinecraftWarp $warp)
    {
        $warp->delete();

        return response()->json();
    }
}
