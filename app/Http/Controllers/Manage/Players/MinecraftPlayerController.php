<?php

namespace App\Http\Controllers\Manage\Players;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Core\Domains\MinecraftUUID\UseCases\LookupMinecraftUUID;
use App\Core\Domains\Mojang\Api\MojangPlayerApi;
use App\Domains\Manage\RendersManageApp;
use App\Http\Controllers\WebController;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class MinecraftPlayerController extends WebController
{
    use RendersManageApp;

    public function index()
    {
        Gate::authorize('viewAny', MinecraftPlayer::class);

        $minecraftPlayers = MinecraftPlayer::with(['account'])
            ->orderBy('created_at', 'desc')
            ->cursorPaginate(50);

        if (request()->wantsJson()) {
            return $minecraftPlayers;
        }
        return $this->inertiaRender('Players/PlayerList', [
            'players' => $minecraftPlayers,
        ]);
    }

    public function show(MinecraftPlayer $player)
    {
        Gate::authorize('view', $player);

        $player->load(['account']);

        return $this->inertiaRender('Players/PlayerShow', compact('player'));
    }

    public function create()
    {
        Gate::authorize('create', MinecraftPlayer::class);

        return $this->inertiaRender('Players/PlayerCreate');
    }

    public function store(Request $request, LookupMinecraftUUID $lookupMinecraftUUID)
    {
        Gate::authorize('create', MinecraftPlayer::class);

        $validated = $request->validate([
            'uuid' => ['required', new MinecraftUUIDRule],
            'alias' => ['required', 'string'],
            'account_id' => ['nullable', 'exists:accounts'],
        ]);

        $uuid = MinecraftUUID::tryParse($validated['uuid']);

        if (MinecraftPlayer::whereUuid($uuid)->exists()) {
            throw ValidationException::withMessages([
                'uuid' => 'A player already exists with this UUID',
            ]);
        }

        $lookup = $lookupMinecraftUUID->fetch($uuid);
        if ($lookup === null) {
            throw ValidationException::withMessages([
                'uuid' => 'Could not find Minecraft account matching this UUID',
            ]);
        }

        $player = MinecraftPlayer::updateOrCreate(
            [
                'uuid' => $uuid->trimmed(),
            ], [
                'alias' => $validated['alias'],
                'account_id' => $request->get(key: 'account_id'),
            ],
        );

        return to_route('manage.players.show', $player)
            ->with(['success' => 'Player created successfully.']);
    }

    public function edit(MinecraftPlayer $player)
    {
        Gate::authorize('update', $player);

        return $this->inertiaRender('Players/PlayerEdit', compact('player'));
    }

    public function update(Request $request, MinecraftPlayer $player)
    {
        Gate::authorize('update', $player);

        $validated = $request->validate([
            'uuid' => ['required', new MinecraftUUIDRule],
            'alias' => ['required', 'string'],
            'account_id' => ['nullable', 'exists:accounts'],
        ]);

        $uuid = MinecraftUUID::tryParse($validated['uuid']);
        $exists = MinecraftPlayer::whereUuid($uuid)
            ->whereNot(MinecraftPlayer::primaryKey(), $player->getKey())
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'uuid' => 'A player already exists with this UUID',
            ]);
        }

        $player->update($validated);

        return to_route('manage.players.show', $player)
            ->with(['success' => 'Player updated successfully.']);
    }
}
