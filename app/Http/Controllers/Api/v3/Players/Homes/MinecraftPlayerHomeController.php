<?php

namespace App\Http\Controllers\Api\v3\Players\Homes;

use App\Core\Domains\MinecraftCoordinate\MinecraftCoordinate;
use App\Core\Domains\MinecraftCoordinate\ValidatesCoordinates;
use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\Pagination\HasPaginatedApi;
use App\Domains\Homes\Exceptions\HomeLimitReachedException;
use App\Domains\Homes\Services\HomeService;
use App\Http\Controllers\ApiController;
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

    public function index(Request $request, MinecraftUUID $minecraftUUID)
    {
        $validated = $request->validate([
            ...$this->paginationRules,
        ]);

        $player = MinecraftPlayer::whereUuid($minecraftUUID)->firstOrFail();
        $pageSize = $this->pageSize($validated);

        return MinecraftHome::orderBy('name', 'asc')
            ->where('player_id', $player->getKey())
            ->paginate($pageSize);
    }

    public function show(
        Request $request,
        MinecraftUUID $minecraftUUID,
        MinecraftHome $home,
    ) {
        $player = MinecraftPlayer::whereUuid($minecraftUUID)->firstOrFail();

        return $this->homeService->show($home, $player);
    }

    public function store(Request $request, MinecraftUUID $minecraftUUID)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            ...$this->coordinateRules,
        ]);

        $player = MinecraftPlayer::whereUuid($minecraftUUID)->firstOrFail();

        try {
            return $this->homeService->create(
                player: $player,
                coordinate: MinecraftCoordinate::fromValidatedRequest($validated),
                name: $validated['name'],
            );
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

        return $this->homeService->update(
            home: $home,
            player: $player,
            coordinate: MinecraftCoordinate::fromValidatedRequest($validated),
            name: $validated['name'],
        );
    }

    public function destroy(
        Request $request,
        MinecraftUUID $minecraftUUID,
        MinecraftHome $home,
    ) {
        $player = MinecraftPlayer::whereUuid($minecraftUUID)->firstOrFail();

        $this->homeService->delete($home, $player);

        return response()->json();
    }
}
