<?php

namespace App\Http\Controllers\Manage\Bans;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Domains\Manage\RendersManageApp;
use App\Domains\MinecraftEventBus\Events\IpAddressBanned;
use App\Http\Controllers\WebController;
use App\Models\GameIPBan;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class GameIPBanController extends WebController
{
    use RendersManageApp;

    public function index(Request $request)
    {
        Gate::authorize('viewAny', GameIPBan::class);

        $bans = function () {
            return GameIPBan::with('bannerPlayer')
                ->orderBy('created_at', 'desc')
                ->cursorPaginate(50);
        };

        if (request()->wantsJson()) {
            return $bans();
        }
        return $this->inertiaRender('IPBans/IPBanList', [
            'bans' => Inertia::defer($bans),
        ]);
    }

    public function create(Request $request)
    {
        Gate::authorize('create', GameIPBan::class);

        return $this->inertiaRender('IPBans/IPBanCreate');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', GameIPBan::class);

        $validated = $request->validate([
            'banner_uuid' => ['required', new MinecraftUUIDRule],
            'banner_alias' => ['required'],
            'ip_address' => 'required',
            'reason' => 'required',
            'created_at' => ['required', 'date'],
        ]);

        $bannerUuid = MinecraftUUID::tryParse($validated['banner_uuid']);
        $bannerPlayer = MinecraftPlayer::firstOrCreate($bannerUuid, alias: $validated['banner_alias']);
        $validated['banner_player_id'] = $bannerPlayer->getKey();

        $ban = GameIPBan::create($validated);

        IpAddressBanned::dispatch($ban);

        return to_route('manage.ip-bans.index')
            ->with(['success' => 'Ban created successfully']);
    }

    public function edit(GameIPBan $ipBan)
    {
        Gate::authorize('update', GameIPBan::class);

        $ipBan->load('bannerPlayer');

        return $this->inertiaRender('IPBans/IPBanEdit', [
            'ban' => $ipBan,
        ]);
    }

    public function update(Request $request, GameIPBan $ipBan)
    {
        Gate::authorize('update', $ipBan);

        $validated = $request->validate([
            'banner_uuid' => ['required', new MinecraftUUIDRule],
            'banner_alias' => ['required'],
            'ip_address' => 'required',
            'reason' => 'required',
            'created_at' => ['required', 'date'],
        ]);

        $ipBan->update($validated);

        return to_route('manage.ip-bans.index')
            ->with(['success' => 'Ban updated successfully']);
    }

    public function destroy(Request $request, GameIPBan $ipBan)
    {
        Gate::authorize('delete', $ipBan);

        $ipBan->delete();

        return to_route('manage.ip-bans.index')
            ->with(['success' => 'Ban deleted successfully']);
    }
}
