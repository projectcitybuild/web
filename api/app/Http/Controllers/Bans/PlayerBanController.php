<?php

namespace App\Http\Controllers\Bans;

use App\Actions\GetOrCreatePlayer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bans\PlayerBanStoreRequest;
use App\Models\Eloquent\PlayerBan;
use App\Models\Events\BanCreatedEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class PlayerBanController extends Controller
{
    private const DEFAULT_RELATIONS = ['bannedPlayer', 'bannerPlayer', 'unbannerPlayer'];

    public function ban(PlayerBanStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $bannerUUID = $validated->get('banner_player_uuid');
        $bannerPlayer = null;
        if ($bannerUUID !== null) {
            $bannerPlayer = (new GetOrCreatePlayer())->run(uuid: $bannerUUID);
        }

        $bannedPlayer = (new GetOrCreatePlayer())
            ->run(uuid: $validated->get('banned_player_uuid'));

        $activeBan = PlayerBan::where('banned_player_id', $bannedPlayer->getKey())
            ->active()
            ->exists();

        if ($activeBan) {
            throw ValidationException::withMessages([
                'banned_player_uuid' => ['This UUID is already banned'],
            ]);
        }

        $ban = PlayerBan::create([
            'banned_player_id' => $bannedPlayer->getKey(),
            'banned_alias_at_time' => $validated->get('banned_alias_at_time'),
            'banner_player_id' => $bannerPlayer?->getKey(),
            'reason' => $validated->get('reason'),
            'expires_at' => null, // TODO
        ]);

        BanCreatedEvent::dispatch($ban);

        return response()->json($ban);
    }
}
