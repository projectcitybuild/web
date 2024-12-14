<?php

namespace App\Http\Controllers\Manage\Bans;

use App\Domains\MinecraftEventBus\Events\IpAddressBanned;
use App\Http\Controllers\WebController;
use App\Models\GameIPBan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GameIPBanController extends WebController
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', GameIPBan::class);

        $bans = GameIPBan::with('bannerPlayer')
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        return view('manage.pages.ip-bans.index')
            ->with(compact('bans'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', GameIPBan::class);

        $ban = new GameIPBan();

        return view('manage.pages.ip-bans.create')
            ->with(compact('ban'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', GameIPBan::class);

        $validated = $request->validate([
            'banner_player_id' => 'required|max:60',
            'ip_address' => 'required|ip',
            'reason' => 'required|string',
            'created_at' => 'required|date',
            'updated_at' => 'required|date',
        ]);

        $ban = GameIPBan::create($validated);

        IpAddressBanned::dispatch($ban);

        return redirect(route('manage.ip-bans.index'));
    }

    public function edit(GameIPBan $ban)
    {
        Gate::authorize('update', GameIPBan::class);

        return view('manage.pages.ip-bans.edit')
            ->with(compact('ban'));
    }

    public function update(Request $request, GameIPBan $ban)
    {
        Gate::authorize('update', $ban);

        $validated = $request->validate([
            'banner_player_id' => 'required|max:60',
            'ip_address' => 'required|ip',
            'reason' => 'required|string',
            'created_at' => 'required|date',
            'updated_at' => 'required|date',
        ]);

        $ban->update($validated);

        return redirect(route('manage.ip-bans.index'));
    }

    public function destroy(Request $request, GameIPBan $ban)
    {
        Gate::authorize('delete', $ban);

        $ban->delete();

        return redirect(route('manage.ip-bans.index'));
    }
}
