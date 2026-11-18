<?php

namespace App\Http\Controllers\Api\v3\Players\Homes;

use App\Core\Domains\MinecraftCoordinate\MinecraftCoordinate;
use App\Core\Domains\MinecraftCoordinate\ValidatesCoordinates;
use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\Pagination\HasPaginatedApi;
use App\Domains\Homes\Exceptions\HomeLimitReachedException;
use App\Domains\Homes\Services\HomeService;
use App\Http\Controllers\ApiController;
use App\Http\Resources\MinecraftHomeResource;
use App\Models\MinecraftHome;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class MinecraftPlayerHomeController extends ApiController
{
    use HasPaginatedApi;
    use ValidatesCoordinates;

    public function __construct(
        private readonly HomeService $homeService,
    ) {}

    /**
     * @response array{
     *     current_page: int,
     *     data: list<MinecraftHomeResource>,
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
    public function index(Request $request, MinecraftUUID $minecraftUUID)
    {
        $validated = $request->validate([
            ...$this->paginationRules,
        ]);

        $player = MinecraftPlayer::whereUuid($minecraftUUID)->firstOrFail();
        $pageSize = $this->pageSize($validated);

        return $this->paginatedResource(
            MinecraftHome::orderBy('name', 'asc')
                ->where('player_id', $player->id)
                ->paginate($pageSize),
            MinecraftHomeResource::class,
            $request,
        );
    }

    public function show(
        Request $request,
        MinecraftUUID $minecraftUUID,
        MinecraftHome $home,
    ) {
        $player = MinecraftPlayer::whereUuid($minecraftUUID)->firstOrFail();

        return new MinecraftHomeResource($this->homeService->show($home, $player));
    }

    public function store(Request $request, MinecraftUUID $minecraftUUID)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            ...$this->coordinateRules,
        ]);

        $player = MinecraftPlayer::whereUuid($minecraftUUID)->firstOrFail();

        try {
            return new MinecraftHomeResource($this->homeService->create(
                player: $player,
                coordinate: MinecraftCoordinate::fromValidatedRequest($validated),
                name: $validated['name'],
            ));
        } catch (HomeLimitReachedException $e) {
            throw ValidationException::withMessages([
                'error' => [$e->getMessage()],
            ]);
        }
    }

    public function update(
        Request $request,
        MinecraftUUID $minecraftUUID,
        MinecraftHome $home,
    ) {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            ...$this->coordinateRules,
        ]);

        $player = MinecraftPlayer::whereUuid($minecraftUUID)->firstOrFail();

        return new MinecraftHomeResource($this->homeService->update(
            home: $home,
            player: $player,
            coordinate: MinecraftCoordinate::fromValidatedRequest($validated),
            name: $validated['name'],
        ));
    }

    public function destroy(
        Request $request,
        MinecraftUUID $minecraftUUID,
        MinecraftHome $home,
    ) {
        $player = MinecraftPlayer::whereUuid($minecraftUUID)->firstOrFail();

        $this->homeService->delete($home, $player);

        return response()->json([]);
    }
}
