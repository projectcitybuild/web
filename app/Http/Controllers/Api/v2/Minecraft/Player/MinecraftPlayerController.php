<?php

namespace App\Http\Controllers\Api\v2\Minecraft\Player;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\Badges\UseCases\GetBadges;
use App\Domains\Bans\Services\IPBanService;
use App\Domains\Groups\Services\PlayerGroupsAggregator;
use App\Http\Controllers\ApiController;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** @deprecated */
final class MinecraftPlayerController extends ApiController
{
    public function __construct(
        private readonly PlayerGroupsAggregator $playerGroupsAggregator,
    ) {}

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

        if ($player !== null) {
            $player->last_connected_at = now();
        }
        $account = $player?->account;

        $groups = optional($account, fn ($it) => $this->playerGroupsAggregator->get($it))
            ?? collect();

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
