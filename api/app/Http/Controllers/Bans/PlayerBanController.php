<?php

namespace App\Http\Controllers\Bans;

use App\Actions\Bans\CreatePlayerBan;
use App\Actions\Bans\CreatePlayerUnban;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bans\PlayerBanDeleteRequest;
use App\Http\Requests\Bans\PlayerBanStoreRequest;
use App\Models\Eloquent\Player;
use App\Models\Eloquent\PlayerBan;
use App\Models\MinecraftUUID;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class PlayerBanController extends Controller
{
    public function show(MinecraftUUID $uuid): JsonResponse
    {
        $player = Player::uuid($uuid)->first();
        if ($player === null) {
            return response()->json(null);
        }
        $ban = PlayerBan::forPlayer($player)->active()->first();
        return response()->json($ban);
    }

    public function store(
        PlayerBanStoreRequest $request,
        CreatePlayerBan $createPlayerBan,
    ): JsonResponse {
        $ban = $createPlayerBan->create($request->playerBan());

        return response()->json($ban);
    }

    public function delete(
        PlayerBanDeleteRequest $request,
        CreatePlayerUnban $createPlayerUnban,
    ): JsonResponse {
        try {
            $ban = $createPlayerUnban->create($request->playerUnban());
        } catch (ModelNotFoundException) {
            throw ValidationException::withMessages([
               'banned_player_uuid' => ['Player is not currently banned'],
            ]);
        }

        return response()->json($ban);
    }
}
