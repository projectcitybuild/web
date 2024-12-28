<?php

namespace App\Http\Controllers\Manage\Bans;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Domains\Bans\Data\UnbanType;
use App\Domains\MinecraftEventBus\Events\MinecraftUuidBanned;
use App\Http\Controllers\WebController;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class GamePlayerBanController extends WebController
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', GamePlayerBan::class);

        $bans = GamePlayerBan::with('bannedPlayer', 'bannerPlayer', 'unbannerPlayer')
            ->orderBy('created_at', 'desc')
            ->cursorPaginate(50);

        if (request()->wantsJson()) {
            return $bans;
        }
        return Inertia::render('PlayerBans/PlayerBanList', compact('bans'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', GamePlayerBan::class);

        $account = $request->user();
        $account->load('minecraftAccount');

        return Inertia::render('PlayerBans/PlayerBanCreate', [
            'account' => $account,
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('create', GamePlayerBan::class);

        $validated = $request->validate([
            'banned_uuid' => ['required', new MinecraftUUIDRule],
            'banned_alias' => ['required', 'string'],
            'banner_uuid' => ['nullable', new MinecraftUUIDRule],
            'banner_alias' => ['nullable', 'string'],
            'reason' => 'required',
            'expires_at' => ['nullable', 'date'],
            'created_at' => ['required', 'date'],
        ]);

        $bannedUuid = MinecraftUUID::tryParse($validated['banned_uuid']);
        $bannedPlayer = MinecraftPlayer::firstOrCreate($bannedUuid, alias: $validated['banned_alias']);
        $validated['banned_player_id'] = $bannedPlayer->getKey();

        // For backwards compatibility
        // TODO: remove this column later
        $validated['banned_alias_at_time'] = $validated['banned_alias'];

        if ($request->get('banner_uuid') !== null) {
            $bannerUuid = MinecraftUUID::tryParse($validated['banner_uuid']);
            $bannerPlayer = MinecraftPlayer::firstOrCreate($bannerUuid, alias: $validated['banner_alias']);
            $validated['banner_player_id'] = $bannerPlayer->getKey();
        }

        $ban = GamePlayerBan::create($validated);

        MinecraftUuidBanned::dispatch($ban);

        return to_route('manage.player-bans.index');
    }

    public function edit(GamePlayerBan $playerBan)
    {
        Gate::authorize('update', $playerBan);

        $playerBan->load('bannedPlayer', 'bannerPlayer', 'unbannerPlayer');

        return Inertia::render('PlayerBans/PlayerBanEdit', [
            'ban' => $playerBan,
        ]);
    }

    public function update(Request $request, GamePlayerBan $playerBan)
    {
        Gate::authorize('update', $playerBan);

        $validated = $request->validate([
            'banned_uuid' => ['required', new MinecraftUUIDRule],
            'banned_alias' => ['required', 'string'],
            'banner_uuid' => ['nullable', new MinecraftUUIDRule],
            'banner_alias' => ['nullable', 'string'],
            'reason' => 'required',
            'expires_at' => ['nullable', 'date'],
            'created_at' => ['required', 'date'],
            'unbanned_at' => ['nullable', 'date'],
            'unbanner_player_id' => ['nullable', new MinecraftUUIDRule],
            'unban_type' => ['nullable', Rule::in(UnbanType::values())],
        ]);

        $bannedUuid = MinecraftUUID::tryParse($validated['banned_uuid']);
        $bannedPlayer = MinecraftPlayer::firstOrCreate($bannedUuid, alias: $validated['banned_alias']);
        $validated['banned_player_id'] = $bannedPlayer->getKey();

        // TODO: remove this column
        $validated['banned_alias_at_time'] = $validated['banned_alias'];

        if ($request->get('banner_uuid') !== null) {
            $bannerUuid = MinecraftUUID::tryParse($validated['banner_uuid']);
            $bannerPlayer = MinecraftPlayer::firstOrCreate($bannerUuid, alias: $validated['banner_alias']);
            $validated['banner_player_id'] = $bannerPlayer->getKey();
        } else {
            $validated['banner_player_id'] = null;

        }

        $playerBan->update($validated);

        return to_route('manage.player-bans.index');
    }

    public function destroy(Request $request, GamePlayerBan $playerBan)
    {
        Gate::authorize('delete', $playerBan);

        $playerBan->delete();

        return to_route('manage.player-bans.index');
    }
}
