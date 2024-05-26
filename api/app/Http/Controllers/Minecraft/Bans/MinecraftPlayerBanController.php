<?php

namespace App\Http\Controllers\Minecraft\Bans;

use App\Core\Domains\MinecraftUUID\MinecraftUUID;
use App\Core\Exceptions\ModelAlreadyExistsException;
use App\Domains\Bans\Actions\CreatePlayerBan;
use App\Domains\Bans\Actions\CreatePlayerUnban;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bans\PlayerBanDeleteRequest;
use App\Http\Requests\Bans\PlayerBanStoreRequest;
use App\Models\Player;
use App\Models\PlayerBan;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class MinecraftPlayerBanController extends Controller
{
    public function show(MinecraftUUID $uuid): JsonResponse
    {
        $player = Player::uuid($uuid)->firstOrFail();
        $ban = PlayerBan::forPlayer($player)->active()->firstOrFail();
        return response()->json($ban);
    }

    public function store(
        PlayerBanStoreRequest $request,
        CreatePlayerBan $createPlayerBan,
    ): JsonResponse
    {
        $ban = $createPlayerBan->call($request->playerBan());
        return response()->json($ban);
    }

    public function delete(
        PlayerBanDeleteRequest $request,
        CreatePlayerUnban $createPlayerUnban,
    ): JsonResponse
    {
        $ban = $createPlayerUnban->call($request->playerUnban());
        return response()->json($ban);
    }
}
