<?php

namespace App\Http\Controllers\Bans;

use App\Actions\Bans\CreatePlayerBan;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bans\PlayerBanStoreRequest;
use App\Utilities\Traits\HasTimestampInput;
use Illuminate\Http\JsonResponse;

class PlayerBanController extends Controller
{
    use HasTimestampInput;

    public function ban(
        PlayerBanStoreRequest $request,
        CreatePlayerBan $createPlayerBan,
    ): JsonResponse {
        $validated = $request->safe()->collect();

        $ban = $createPlayerBan->create(
            bannedPlayerUUID: $validated->get('banned_player_uuid'),
            bannedPlayerAlias: $validated->get('banned_player_alias'),
            bannerPlayerUUID: $validated->get('banner_player_uuid'),
            reason: $validated->get('reason'),
            expiresAt: self::timestamp(named: 'expires_at', in: $validated),
        );

        return response()->json($ban);
    }
}
