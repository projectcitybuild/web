<?php

namespace App\Http\Controllers\Manage\Players;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Core\Domains\Mojang\Api\MojangPlayerApi;
use App\Http\Controllers\WebController;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class MinecraftPlayerController extends WebController
{
    public function index()
    {
        Gate::authorize('viewAny', MinecraftPlayer::class);

        $minecraftPlayers = MinecraftPlayer::with(['account'])
            ->paginate(50);

        return view('manage.pages.minecraft-player.index')
            ->with(compact('minecraftPlayers'));
    }

    public function show(MinecraftPlayer $minecraftPlayer)
    {
        Gate::authorize('view', $minecraftPlayer);

        $minecraftPlayer->load(['account', 'gamePlayerBans', 'gamePlayerBans.bannedPlayer', 'gamePlayerBans.bannerPlayer']);

        return view('manage.pages.minecraft-player.show')->with(compact('minecraftPlayer'));
    }

    public function create()
    {
        Gate::authorize('create', MinecraftPlayer::class);

        return view('manage.pages.minecraft-player.create');
    }

    public function store(Request $request, MojangPlayerApi $api)
    {
        Gate::authorize('create', MinecraftPlayer::class);

        $request->validate([
            'uuid' => ['required', new MinecraftUUIDRule],
            'account_id' => ['nullable', 'exists:accounts'],
        ]);

        $uuid = MinecraftUUID::tryParse($request->uuid);

        if ($api->getNameHistoryOf($uuid->trimmed()) == null) {
            throw ValidationException::withMessages([
                'uuid' => 'Not an active Minecraft UUID',
            ]);
        }

        $mcPlayer = MinecraftPlayer::updateOrCreate(
            ['uuid' => $uuid->trimmed()],
            ['account_id' => $request->account_id],
        );

        return redirect(route('manage.minecraft-players.show', $mcPlayer));
    }

    public function edit(MinecraftPlayer $minecraftPlayer)
    {
        Gate::authorize('update', $minecraftPlayer);

        return view('manage.pages.minecraft-player.edit')
            ->with(compact('minecraftPlayer'));
    }

    public function update(Request $request, MinecraftPlayer $minecraftPlayer)
    {
        Gate::authorize('update', $minecraftPlayer);

        $validated = $request->validate([
            'account_id' => ['nullable', 'exists:accounts'],
        ]);

        $minecraftPlayer->update($validated);

        return redirect(route('manage.minecraft-players.show', $minecraftPlayer));
    }

    public function destroy(MinecraftPlayer $minecraftPlayer)
    {
        Gate::authorize('delete', $minecraftPlayer);

        //
    }
}
