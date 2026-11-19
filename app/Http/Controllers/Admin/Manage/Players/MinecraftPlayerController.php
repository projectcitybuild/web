<?php

namespace App\Http\Controllers\Admin\Manage\Players;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Core\Domains\MinecraftUUID\UseCases\LookupMinecraftUUID;
use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\Admin\SortsAdminLists;
use App\Http\Controllers\WebController;
use App\Http\Data\MinecraftPlayerData;
use App\Http\Filters\LikeFilter;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class MinecraftPlayerController extends WebController
{
    use AuthorizesPermissions;
    use RendersAdminApp;
    use SortsAdminLists;

    public function index(Request $request)
    {
        $this->requires(WebManagePermission::PLAYERS_VIEW);

        $sortableColumns = [
            'id' => 'id',
            'uuid' => 'uuid',
            'alias' => 'alias',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
            'last_seen_at' => 'last_seen_at',
        ];
        $sortState = $this->adminListSortState($request, $sortableColumns);

        $players = function () use ($request, $sortableColumns, $sortState) {
            $pipes = [
                new LikeFilter('alias', $request->query->get('alias')),
                new LikeFilter('uuid', $request->query->get('uuid')),
            ];
            $query = Pipeline::send(MinecraftPlayer::query())
                ->through($pipes)
                ->thenReturn()
                ->with(['account']);

            $this->applyAdminListSort($query, $sortableColumns, $sortState, 'created_at');

            return $query->paginate(50);
        };

        if (request()->wantsJson()) {
            return $players();
        }
        return $this->inertiaRender('Players/PlayerList', [
            'players' => Inertia::defer(fn () => $players()->through(
                fn (MinecraftPlayer $player): array => MinecraftPlayerData::fromModel($player)->toArray(),
            )),
            ...$sortState,
        ]);
    }

    public function show(MinecraftPlayer $player)
    {
        $this->requires(WebManagePermission::PLAYERS_VIEW);

        $player->load(['account']);

        return $this->inertiaRender('Players/PlayerShow', [
            'player' => MinecraftPlayerData::fromModel($player),
        ]);
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

        return $this->inertiaRender('Players/PlayerEdit', [
            'player' => MinecraftPlayerData::fromModel($player),
        ]);
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
            ->whereNot(MinecraftPlayer::primaryKey(), $player->id)
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
