<?php

namespace App\Http\Controllers\Api\v2;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Domains\Bans\Data\CreatePlayerBan;
use App\Domains\Bans\Data\UnbanType;
use App\Domains\Bans\Data\UpdatePlayerBan;
use App\Domains\Bans\Services\PlayerBanService;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

final class GamePlayerBanController extends ApiController
{
    public function __construct(
        private readonly PlayerBanService $playerBanService,
    ) {}

    public function store(Request $request)
    {
        $validated = collect($request->validate([
            'banned_uuid' => ['required', new MinecraftUUIDRule],
            'banned_alias' => ['required', 'string'],
            'banner_uuid' => ['nullable', new MinecraftUUIDRule],
            'banner_alias' => ['nullable', 'string'],
            'reason' => 'required',
            'additional_info' => 'nullable',
            'expires_at' => ['nullable', 'date'],
            'created_at' => ['nullable', 'date'],
            'unbanned_at' => ['nullable', 'date'],
            'unbanner_uuid' => ['nullable', new MinecraftUUIDRule],
            'unbanner_alias' => ['nullable', 'string'],
            'unban_type' => ['nullable', Rule::in(UnbanType::values())],
        ]));

        return $this->playerBanService->store(
            new CreatePlayerBan(
                bannedUuid: new MinecraftUUID($validated->get('banned_uuid')),
                bannedAlias: $validated->get('banned_alias'),
                bannerUuid: optional($validated->get('banner_uuid'), fn ($it) => new MinecraftUUID($it)),
                bannerAlias: $validated->get('banner_alias'),
                reason: $validated->get('reason'),
                additionalInfo: $validated->get('additional_info'),
                expiresAt: optional($validated->get('expires_at'), fn ($it) => Carbon::parse($it)),
                createdAt: optional($validated->get('created_at'), fn ($it) => Carbon::parse($it)) ?? now(),
                unbannedAt: optional($validated->get('unbanned_at'), fn ($it) => Carbon::parse($it)),
                unbannerUuid: optional($validated->get('unbanner_uuid'), fn ($it) => new MinecraftUUID($it)),
                unbannerAlias: $validated->get('unbanner_alias'),
            ),
        );
    }

    public function update(Request $request, int $banId)
    {
        $validated = collect($request->validate([
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
        ]));

        return $this->playerBanService->update(
            new UpdatePlayerBan(
                id: $banId,
                bannedUuid: new MinecraftUUID($validated->get('banned_uuid')),
                bannedAlias: $validated->get('banned_alias'),
                bannerUuid: optional($validated->get('banner_uuid'), fn ($it) => new MinecraftUUID($it)),
                bannerAlias: $validated->get('banner_alias'),
                reason: $validated->get('reason'),
                additionalInfo: $validated->get('additional_info'),
                expiresAt: optional($validated->get('expires_at'), fn ($it) => Carbon::parse($it)),
                createdAt: Carbon::parse($validated->get('created_at')),
                unbannedAt: optional($validated->get('unbanned_at'), fn ($it) => Carbon::parse($it)),
                unbannerUuid: optional($validated->get('unbanner_uuid'), fn ($it) => new MinecraftUUID($it)),
                unbannerAlias: $validated->get('unbanner_alias'),
                unbanType: optional($validated->get('unban_type'), fn ($it) => UnbanType::from($it)),
            ),
        );
    }

    public function delete(Request $request, int $banId)
    {
        $this->playerBanService->delete(id: $banId);

        return response()->json();
    }
}
