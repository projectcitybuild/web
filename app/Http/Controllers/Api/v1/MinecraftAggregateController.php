<?php

namespace App\Http\Controllers\Api\v1;

use App\Core\Data\Exceptions\NotFoundException;
use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\PlayerLookup\Data\PlayerIdentifier;
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
    public function show(
        Request $request,
        string $uuid,
        GetActivePlayerBan $getBan,
        GetBadges $getBadges,
        GetDonationTiers $getDonationTier,
        GetActiveIPBan $getActiveIPBan,
    ): JsonResponse {
        $this->validateRequest($request->all(), [
            'ip' => 'ip',
        ]);

        $uuid = new MinecraftUUID($uuid);

        $ban = $getBan->execute(uuid: $uuid);
        $badges = $getBadges->execute(uuid: $uuid);

        try {
            $donationTiers = $getDonationTier->execute(uuid: $uuid);
        } catch (NotFoundException) {
            $donationTiers = [];
        }

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

        if ($existingPlayer === null || $existingPlayer->account === null) {
            return null;
        }
        // Force load groups
        $existingPlayer->account->groups;
        $existingPlayer->touchLastSyncedAt();

        if ($existingPlayer->account->groups->isEmpty()) {
            $existingPlayer->account->groups = collect(Group::whereDefault()->first());
        }

        return $existingPlayer->account;
    }
}
