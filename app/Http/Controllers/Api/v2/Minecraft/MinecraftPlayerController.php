<?php

namespace App\Http\Controllers\Api\v2\Minecraft;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\Badges\UseCases\GetBadges;
use App\Domains\Bans\UseCases\GetActiveIPBan;
use App\Domains\Bans\UseCases\GetActivePlayerBan;
use App\Domains\Donations\UseCases\GetDonationTiers;
use App\Http\Controllers\ApiController;
use App\Models\Group;
use App\Models\MinecraftPlayer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MinecraftPlayerController extends ApiController
{
    public function __invoke(
        Request $request,
        MinecraftUUID $uuid,
        GetActivePlayerBan $getBan,
        GetBadges $getBadges,
        GetDonationTiers $getDonationTier,
        GetActiveIPBan $getActiveIPBan,
    ): JsonResponse {
        $request->validate([
            'ip' => 'ip',
        ]);

        $ban = $getBan->execute(uuid: $uuid);
        $badges = $getBadges->execute(uuid: $uuid);

        $donationTiers = rescue(
            callback: fn () => $getDonationTier->execute(uuid: $uuid),
            rescue: [],
            report: false,
        );

        $ipBan = null;
        if ($request->has('ip')) {
            $ipBan = $getActiveIPBan->execute(ip: $request->get('ip'));
        }

        $player = MinecraftPlayer::whereUuid($uuid)->first();
        $player?->touchLastSyncedAt();

        $account = $player?->account;
        if ($account !== null && $account->groups->isEmpty()) {
            // TODO: change this to model attribute
            $account->groups->push(Group::whereDefault()->first());
        }

        return response()->json([
            'account' => $account,
            'player' => $player?->withoutRelations(),
            'ban' => $ban,
            'badges' => $badges,
            'donation_tiers' => $donationTiers,
            'ip_ban' => $ipBan,
        ]);
    }
}
