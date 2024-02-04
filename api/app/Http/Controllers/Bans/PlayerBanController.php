<?php

namespace App\Http\Controllers\Bans;

use App\Http\Controllers\Controller;
use App\Models\Eloquent\Player;
use App\Models\Eloquent\PlayerBan;
use App\Rules\TimestampPastNow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlayerBanController extends Controller
{
    private const DEFAULT_RELATIONS = ['bannedPlayer', 'bannerPlayer', 'unbannerPlayer'];

    public function ban(Request $request): JsonResponse
    {
        $this->validateRequest($request->all(), [
            'banned_player_uuid' => ['required', 'uuid'],
            'banned_player_alias' => 'required|string',
            'banner_player_uuid' => 'max:60',
            'banner_player_alias' => 'string',
            'reason' => 'nullable|string',
            'expires_at' => ['integer', new TimestampPastNow],
        ]);

        // TODO: validate if valid Minecraft UUID (lookup db?)

        $bannerPlayer = Player::where('uuid', $request->get('banner_player_uuid'))->first();
        if ($bannerPlayer === null) {
            $bannerPlayer = Player::create(); // TODO
        }

        $bannedPlayer = Player::where('uuid', $request->get('banned_player_uuid'))->first();
        if ($bannedPlayer === null) {
            $bannedPlayer = Player::create(); // TODO
        }

        $activeBan = PlayerBan::where('banned_player_id', $bannedPlayer->getKey())
            ->active()
            ->exists();

        if ($activeBan) {
            // TODO: return error body
            return response()->json(['error' => 'todo']);
        }

        $ban = PlayerBan::create([

        ]);

        return response()->json($ban);
    }
}
