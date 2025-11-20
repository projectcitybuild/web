<?php

namespace App\Http\Controllers\Api\v2\Minecraft\Player;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\Badges\UseCases\GetBadges;
use App\Domains\Bans\Services\IPBanService;
use App\Http\Controllers\ApiController;
use App\Models\GamePlayerBan;
use App\Models\Group;
use App\Models\MinecraftPlayer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MinecraftPlayerController extends ApiController
{
    public function __invoke(
        Request $request,
        MinecraftUUID $uuid,
        GetBadges $getBadges,
        IPBanService $ipBanService,
    ): JsonResponse {
        $request->validate([
            'ip' => 'ip',
        ]);

        $player = MinecraftPlayer::whereUuid($uuid)
            ->with(['account.groups', 'account.donationPerks.donationTier.group'])
            ->first();

        $player?->touchLastSyncedAt();
        $account = $player?->account;

        $donationTiers = optional($account, function ($account) {
            return $account->donationPerks
                ->where('is_active', true)
                ->where('expires_at', '>', now())
                ->map(fn ($it) => $it->donationTier);
        }) ?: collect();

        $groups = $account?->groups ?: collect();
        if ($account !== null && $account->groups->isEmpty()) {
            $groups->add(Group::whereDefault()->first());
        }

        $donorGroups = $donationTiers->map(fn ($tier) => $tier->group);
        if (!$donorGroups->isEmpty()) {
            $groups = $groups->merge($donorGroups);
        }

        return response()->json([
            'account' => $account?->withoutRelations(),
            'player' => $player?->withoutRelations(),
            'groups' => $groups,
            'ban' => optional($player, function ($player) {
                return GamePlayerBan::whereBannedPlayer($player)
                    ->active()
                    ->first();
            }),
            'badges' => optional($player, fn ($player) => $getBadges->execute($player)) ?? [],
            'ip_ban' => $request->has('ip')
                ? $ipBanService->find(ip: $request->get('ip'))
                : null,
        ]);
    }
}
