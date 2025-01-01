<?php

namespace App\Http\Controllers\Manage\Players;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Core\Domains\MinecraftUUID\UseCases\LookupMinecraftUUID;
use App\Core\Domains\Mojang\Api\MojangPlayerApi;
use App\Http\Controllers\WebController;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class MinecraftPlayerController extends WebController
{
    public function index()
    {
        Gate::authorize('viewAny', MinecraftPlayer::class);

        $minecraftPlayers = MinecraftPlayer::with(['account'])
            ->cursorPaginate(50);

        if (request()->wantsJson()) {
            return $minecraftPlayers;
        }
        return Inertia::render('Players/PlayerList', [
            'players' => $minecraftPlayers,
        ]);
    }

    public function show(MinecraftPlayer $minecraftPlayer)
    {
        Gate::authorize('view', $minecraftPlayer);

        $minecraftPlayer->load(['account', 'gamePlayerBans', 'gamePlayerBans.bannedPlayer', 'gamePlayerBans.bannerPlayer']);

        return Inertia::render('Players/PlayerShow', [
            'player' => $minecraftPlayer,
        ]);
    }

    public function create()
    {
        Gate::authorize('create', MinecraftPlayer::class);

        return Inertia::render('Players/PlayerCreate');
    }

    public function store(Request $request, LookupMinecraftUUID $lookupMinecraftUUID)
    {
        Gate::authorize('create', MinecraftPlayer::class);

        $request->validate([
            'uuid' => ['required', new MinecraftUUIDRule],
            'account_id' => ['nullable', 'exists:accounts'],
        ]);

        $uuid = MinecraftUUID::tryParse($request->uuid);

        $lookup = $lookupMinecraftUUID->fetch($uuid);
        if ($lookup === null) {
            throw ValidationException::withMessages([
                'uuid' => 'Could not find Minecraft account matching this UUID',
            ]);
        }

        $player = MinecraftPlayer::updateOrCreate(
            ['uuid' => $uuid->trimmed()],
            ['account_id' => $request->account_id],
        );

        return to_route('manage.minecraft-players.show', $player)
            ->with(['success' => 'Player created successfully.']);
    }

    public function edit(MinecraftPlayer $minecraftPlayer)
    {
        Gate::authorize('update', $minecraftPlayer);

        return Inertia::render('Players/PlayerEdit', [
            'player' => $minecraftPlayer,
        ]);
    }

    public function update(Request $request, MinecraftPlayer $minecraftPlayer)
    {
        Gate::authorize('update', $minecraftPlayer);

        $validated = $request->validate([
            'account_id' => ['nullable', 'exists:accounts'],
        ]);

        $minecraftPlayer->update($validated);

        return to_route('manage.minecraft-players.show', $minecraftPlayer)
            ->with(['success' => 'Player updated successfully.']);
    }

    public function destroy(MinecraftPlayer $minecraftPlayer)
    {
        Gate::authorize('delete', $minecraftPlayer);

        // Not supported yet
    }
}
