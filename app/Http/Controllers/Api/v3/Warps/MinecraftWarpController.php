<?php

namespace App\Http\Controllers\Api\v3\Warps;

use App\Core\Domains\MinecraftCoordinate\ValidatesCoordinates;
use App\Core\Domains\Pagination\HasPaginatedApi;
use App\Http\Controllers\ApiController;
use App\Http\Data\MinecraftWarpData;
use App\Models\MinecraftWarp;
use Dedoc\Scramble\Attributes\Response as OpenApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class MinecraftWarpController extends ApiController
{
    use HasPaginatedApi;
    use ValidatesCoordinates;

    /**
     * @response array{
     *     current_page: int,
     *     data: list<MinecraftWarpData>,
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

        return $this->paginatedData(
            MinecraftWarp::orderBy('name', 'asc')->paginate($pageSize),
            MinecraftWarpData::class,
        );
    }

    /**
     * @response list<MinecraftWarpData>
     */
    public function bulk(Request $request)
    {
        return MinecraftWarpData::collect(MinecraftWarp::orderBy('name', 'asc')->get())->toArray();
    }

    public function show(Request $request, MinecraftWarp $warp)
    {
        return MinecraftWarpData::from($warp);
    }

    #[OpenApiResponse(status: 201, type: MinecraftWarpData::class)]
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'alpha_dash', Rule::unique(MinecraftWarp::tableName())],
            ...$this->coordinateRules,
        ]);

        return response()->json(
            MinecraftWarpData::from(MinecraftWarp::create($request->all()))->toArray(),
            201,
        );
    }

    public function update(Request $request, MinecraftWarp $warp)
    {
        $request->validate([
            'name' => ['required', 'string', 'alpha_dash', Rule::unique(MinecraftWarp::tableName())->ignore($warp)],
            ...$this->coordinateRules,
        ]);

        $warp->update($request->all());

        return MinecraftWarpData::from($warp);
    }

    public function destroy(Request $request, MinecraftWarp $warp)
    {
        $warp->delete();

        return response()->json([]);
    }
}
