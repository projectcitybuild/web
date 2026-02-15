<?php

namespace App\Http\Controllers\Manage\Players;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Core\Domains\MinecraftUUID\UseCases\LookupMinecraftUUID;
use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Manage\RendersManageApp;
use App\Http\Controllers\WebController;
use App\Http\Filters\LikeFilter;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class MinecraftPlayerController extends WebController
{
    use RendersManageApp;
    use AuthorizesPermissions;

    public function index(Request $request)
    {
        $this->requires(WebManagePermission::PLAYERS_VIEW);

        $players = function () use ($request) {
            $pipes = [
                new LikeFilter('alias', $request->query->get('alias')),
                new LikeFilter('uuid', $request->query->get('uuid')),
            ];
            return Pipeline::send(MinecraftPlayer::query())
                ->through($pipes)
                ->thenReturn()
                ->with(['account'])
                ->orderBy('created_at', 'desc')
                ->paginate(50);
        };

        if (request()->wantsJson()) {
            return $players();
        }
        return $this->inertiaRender('Players/PlayerList', [
            'players' => Inertia::defer($players),
        ]);
    }

    public function show(MinecraftPlayer $player)
    {
        $this->requires(WebManagePermission::PLAYERS_VIEW);

        $player->load(['account']);

        return $this->inertiaRender('Players/PlayerShow', compact('player'));
    }

    public function create()
    {
        $this->requires(WebManagePermission::PLAYERS_EDIT);

        return $this->inertiaRender('Players/PlayerCreate');
    }

    public function store(Request $request, LookupMinecraftUUID $lookupMinecraftUUID)
    {
        $this->requires(WebManagePermission::PLAYERS_EDIT);

        $validated = $request->validate([
            'uuid' => ['required', new MinecraftUUIDRule],
            'alias' => ['required', 'string'],
            'nickname' => 'nullable',
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
                'account_id' => $request->request->get(key: 'account_id'),
            ],
        );

        return to_route('manage.players.show', $player)
            ->with(['success' => 'Player created successfully.']);
    }

    public function edit(MinecraftPlayer $player)
    {
        $this->requires(WebManagePermission::PLAYERS_EDIT);

        return $this->inertiaRender('Players/PlayerEdit', compact('player'));
    }

    public function update(Request $request, MinecraftPlayer $player)
    {
        $this->requires(WebManagePermission::PLAYERS_EDIT);

        $validated = $request->validate([
            'uuid' => ['required', new MinecraftUUIDRule],
            'alias' => ['required', 'string'],
            'nickname' => 'nullable',
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
