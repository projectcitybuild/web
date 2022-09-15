<?php

namespace App\Http\Controllers\Api\v1;

use App\Exceptions\Http\NotFoundException;
use App\Http\ApiController;
use Domain\Badges\UseCases\GetBadges;
use Domain\Bans\UseCases\GetActivePlayerBan;
use Domain\Donations\UseCases\GetDonationTiers;
use Domain\Warnings\UseCases\GetWarnings;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Resources\AccountResource;
use Entities\Resources\DonationPerkResource;
use Entities\Resources\GamePlayerBanResource;
use Entities\Resources\PlayerWarningResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Shared\PlayerLookup\Entities\PlayerIdentifier;

final class MinecraftAggregateController extends ApiController
{
    public function show(
        Request            $request,
        string             $uuid,
        GetActivePlayerBan $getBan,
        GetBadges          $getBadges,
        GetDonationTiers   $getDonationTier,
        GetWarnings        $getWarnings,
    ): JsonResponse {
        $identifier = PlayerIdentifier::minecraftUUID($uuid);

        $ban = $getBan->execute(playerIdentifier: $identifier);
        $badges = $getBadges->execute(identifier: $identifier);
        $warnings = $getWarnings->execute(playerIdentifier: $identifier);

        try {
            $donationTiers = $getDonationTier->execute(uuid: $uuid);
        } catch (NotFoundException) {
            $donationTiers = [];
        }

        $account = $this->getLinkedAccount($uuid);

        return response()->json([
            'data' => [
                'account' => is_null($account) ? null : AccountResource::make($account),
                'ban' => is_null($ban) ? null : GamePlayerBanResource::make($ban),
                'badges' => $badges,
                'donation_tiers' => DonationPerkResource::collection($donationTiers),
                'warnings' => PlayerWarningResource::collection($warnings),
            ],
        ]);
    }

    // TODO: share this logic with MinecraftAuthTokenController
    private function getLinkedAccount(string $uuid): ?Account
    {
        $existingPlayer = MinecraftPlayer::where('uuid', $uuid)->first();

        if ($existingPlayer === null || $existingPlayer->account === null) {
            return null;
        }
        // Force load groups
        $existingPlayer->account->groups;
        $existingPlayer->touchLastSyncedAt();

        return $existingPlayer->account;
    }
}
