<?php

namespace App\Http\Controllers\Api\v3\Warps;

use App\Core\Domains\MinecraftCoordinate\ValidatesCoordinates;
use App\Core\Domains\Pagination\HasPaginatedApi;
use App\Http\Controllers\ApiController;
use App\Http\Resources\MinecraftWarpResource;
use App\Models\MinecraftWarp;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class MinecraftWarpController extends ApiController
{
    use HasPaginatedApi;
    use ValidatesCoordinates;

    /**
     * @response array{
     *     current_page: int,
     *     data: list<MinecraftWarpResource>,
     *     first_page_url: string|null,
     *     from: int|null,
     *     last_page_url: string|null,
     *     last_page: int,
     *     links: list<array{url: string|null, label: string, active: bool}>,
     *     next_page_url: string|null,
     *     path: string|null,
     *     per_page: int,
     *     prev_page_url: string|null,
     *     to: int|null,
     *     total: int
     * }
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            ...$this->paginationRules,
        ]);

        $pageSize = $this->pageSize($validated);

        return $this->paginatedResource(
            MinecraftWarp::orderBy('name', 'asc')->paginate($pageSize),
            MinecraftWarpResource::class,
            $request,
        );
    }

    public function bulk(Request $request)
    {
        return MinecraftWarpResource::collection(MinecraftWarp::orderBy('name', 'asc')->get());
    }

    public function show(Request $request, MinecraftWarp $warp)
    {
        return new MinecraftWarpResource($warp);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'alpha_dash', Rule::unique(MinecraftWarp::tableName())],
            ...$this->coordinateRules,
        ]);

        return new MinecraftWarpResource(MinecraftWarp::create($request->all()));
    }

    public function update(Request $request, MinecraftWarp $warp)
    {
        $request->validate([
            'name' => ['required', 'string', 'alpha_dash', Rule::unique(MinecraftWarp::tableName())->ignore($warp)],
            ...$this->coordinateRules,
        ]);

        $warp->update($request->all());

        return new MinecraftWarpResource($warp);
    }

    public function destroy(Request $request, MinecraftWarp $warp)
    {
        $warp->delete();

        return response()->json([]);
    }
}
