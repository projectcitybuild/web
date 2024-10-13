<?php

namespace App\Http\Controllers\Api\v1;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\Badges\UseCases\GetBadges;
use App\Domains\Bans\UseCases\GetActiveIPBan;
use App\Domains\Bans\UseCases\GetActivePlayerBan;
use App\Domains\Donations\UseCases\GetDonationTiers;
use App\Http\Controllers\ApiController;
use App\Http\Resources\AccountResource;
use App\Http\Resources\DonationPerkResource;
use App\Http\Resources\GameIPBanResource;
use App\Http\Resources\GamePlayerBanResource;
use App\Models\Account;
use App\Models\Group;
use App\Models\MinecraftPlayer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MinecraftAggregateController extends ApiController
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

        $account = $this->getAccount($uuid);

        $ipBan = null;
        if ($request->has('ip')) {
            $ipBan = $getActiveIPBan->execute(ip: $request->get('ip'));
        }

        return response()->json([
            'data' => [
                'account' => is_null($account) ? null : AccountResource::make($account),
                'ban' => is_null($ban) ? null : GamePlayerBanResource::make($ban),
                'badges' => $badges,
                'donation_tiers' => DonationPerkResource::collection($donationTiers),
                'ip_ban' => is_null($ipBan) ? null : GameIPBanResource::make($ipBan),
            ],
        ]);
    }

    private function getAccount(MinecraftUUID $uuid): ?Account
    {
        $existingPlayer = MinecraftPlayer::whereUuid($uuid)->first();
        $existingPlayer?->touchLastSyncedAt();

        $account = $existingPlayer?->account;
        if ($account === null) {
            return null;
        }
        if ($account->groups->isEmpty()) {
            $account->groups = collect(Group::whereDefault()->first());
        }
        return $account;
    }
}
