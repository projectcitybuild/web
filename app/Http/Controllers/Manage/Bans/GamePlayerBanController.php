<?php

namespace App\Http\Controllers\Manage\Bans;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Domains\Bans\Data\CreatePlayerBan;
use App\Domains\Bans\Data\UnbanType;
use App\Domains\Bans\Data\UpdatePlayerBan;
use App\Domains\Bans\Services\PlayerBanService;
use App\Http\Controllers\Manage\RendersManageApp;
use App\Http\Controllers\WebController;
use App\Models\GamePlayerBan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class GamePlayerBanController extends WebController
{
    use RendersManageApp;

    public function __construct(
        private readonly PlayerBanService $playerBanService,
    ) {}

    public function index(Request $request)
    {
        Gate::authorize('viewAny', GamePlayerBan::class);

        $bans = function () {
            return GamePlayerBan::with('bannedPlayer', 'bannerPlayer', 'unbannerPlayer')
                ->orderBy('created_at', 'desc')
                ->paginate(50);
        };

        if (request()->wantsJson()) {
            return $bans();
        }
        return $this->inertiaRender('PlayerBans/PlayerBanList', [
            'bans' => Inertia::defer($bans),
        ]);
    }

    public function create(Request $request)
    {
        Gate::authorize('create', GamePlayerBan::class);

        $account = $request->user();
        $account->load('minecraftAccount');

        return $this->inertiaRender('PlayerBans/PlayerBanCreate', [
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
            'additional_info' => 'nullable',
            'expires_at' => ['nullable', 'date'],
            'created_at' => ['required', 'date'],
        ]);

        $this->playerBanService->store(
            new CreatePlayerBan(
                bannedUuid: new MinecraftUUID($validated['banned_uuid']),
                bannedAlias: $validated['banned_alias'],
                bannerUuid: optional($validated['banner_uuid'], fn($it) => new MinecraftUUID($it)),
                bannerAlias: $validated['banner_alias'],
                reason: $validated['reason'],
                additionalInfo: $validated['additional_info'],
                expiresAt: optional($validated['expires_at'], fn($it) => Carbon::parse($it)),
                createdAt: optional($validated['created_at'], fn($it) => Carbon::parse($it), now()),
                unbannedAt: optional($validated['unbanned_at'], fn($it) => Carbon::parse($it)),
                unbannerUuid: optional($validated['unbanner_uuid'], fn($it) => new MinecraftUUID($it)),
                unbannerAlias: $validated['unbanner_alias'],
            ),
        );

        return to_route('manage.player-bans.index')
            ->with(['success' => 'Ban created successfully.']);
    }

    public function edit(GamePlayerBan $playerBan)
    {
        Gate::authorize('update', $playerBan);

        $playerBan->load('bannedPlayer', 'bannerPlayer', 'unbannerPlayer');

        return $this->inertiaRender('PlayerBans/PlayerBanEdit', [
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
            'additional_info' => 'nullable',
            'expires_at' => ['nullable', 'date'],
            'created_at' => ['required', 'date'],
            'unbanned_at' => ['nullable', 'date'],
            'unbanner_uuid' => ['nullable', new MinecraftUUIDRule],
            'unbanner_alias' => ['nullable', 'string'],
            'unban_type' => ['nullable', Rule::in(UnbanType::values())],
        ]);

        $this->playerBanService->update(
            new UpdatePlayerBan(
                id: $playerBan->getKey(),
                bannedUuid: new MinecraftUUID($validated['banned_uuid']),
                bannedAlias: $validated['banned_alias'],
                bannerUuid: optional($validated['banner_uuid'], fn($it) => new MinecraftUUID($it)),
                bannerAlias: $validated['banner_alias'],
                reason: $validated['reason'],
                additionalInfo: $validated['additional_info'],
                expiresAt: optional($validated['expires_at'], fn($it) => Carbon::parse($it)),
                createdAt: Carbon::parse($validated['created_at']),
                unbannedAt: optional($validated['unbanned_at'], fn($it) => Carbon::parse($it)),
                unbannerUuid: optional($validated['unbanner_uuid'], fn($it) => new MinecraftUUID($it)),
                unbannerAlias: $validated['unbanner_alias'],
            ),
        );

        return to_route('manage.player-bans.index')
            ->with(['success' => 'Ban updated successfully.']);
    }

    public function destroy(Request $request, GamePlayerBan $playerBan)
    {
        Gate::authorize('delete', $playerBan);

        $this->playerBanService->delete(id: $playerBan->getKey());

        return to_route('manage.player-bans.index')
            ->with(['success' => 'Ban deleted successfully.']);
    }
}
