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
            ->cursorPaginate(25);

        if (request()->wantsJson()) {
            return $bans;
        }
        return Inertia::render('Bans/BanList', compact('bans'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', GamePlayerBan::class);

        $account = $request->user();
        $account->load('minecraftAccount');

        return Inertia::render('Bans/BanCreate', [
            'account' => $account,
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('create', GamePlayerBan::class);

        $validated = $request->validate([
            'banned_uuid' => ['required', new MinecraftUUIDRule],
            'banned_alias_at_time' => ['required', 'string'],
            'banner_uuid' => [new MinecraftUUIDRule],
            'reason' => 'required',
            'expires_at' => ['nullable', 'date'],
            'created_at' => ['required', 'date'],
            'unbanned_at' => ['nullable', 'date'],
            'unbanner_player_id' => ['nullable', new MinecraftUUIDRule],
            'unban_type' => ['nullable', Rule::in(UnbanType::values())],
        ], [
            'in' => 'Invalid :attribute given. Must be ['.UnbanType::allJoined().']',
        ]);

        $bannedUuid = MinecraftUUID::tryParse($validated['banned_uuid']);
        $bannedPlayer = MinecraftPlayer::firstOrCreate($bannedUuid, alias: $validated['banned_alias_at_time']);
        $validated['banned_player_id'] = $bannedPlayer->getKey();

        if ($request->has('banner_uuid')) {
            $bannerUuid = MinecraftUUID::tryParse($validated['banner_uuid']);
            $bannerPlayer = MinecraftPlayer::firstOrCreate($bannerUuid);
            $validated['banner_player_id'] = $bannerPlayer->getKey();
        }

        $ban = GamePlayerBan::create($validated);

        MinecraftUuidBanned::dispatch($ban);

        return to_route('manage.player-bans.index');
    }

    public function edit(GamePlayerBan $ban)
    {
        Gate::authorize('update', $ban);

        return view('manage.pages.player-bans.edit')
            ->with(compact('ban'));
    }

    public function update(Request $request, GamePlayerBan $ban)
    {
        Gate::authorize('update', $ban);

        $validated = $request->validate([
            'banned_player_id' => 'required|max:60',
            'banned_alias_at_time' => 'required|string',
            'banner_player_id' => 'required|max:60',
            'reason' => 'required|string',
            'expires_at' => 'nullable|date',
            'created_at' => 'required|date',
            'updated_at' => 'required|date',
            'unbanned_at' => 'nullable|date',
            'unbanner_player_id' => 'nullable|max:60',
            'unban_type' => ['nullable', Rule::in(UnbanType::values())],
        ]);

        $ban->update($validated);

        return redirect(route('manage.player-bans.index'));
    }

    public function destroy(Request $request, GamePlayerBan $ban)
    {
        Gate::authorize('delete', $ban);

        $ban->delete();

        return redirect(route('manage.player-bans.index'));
    }
}
