<?php

namespace App\Http\Controllers\Api\v1;

use App\Exceptions\Http\NotFoundException;
use App\Http\ApiController;
use Domain\Badges\UseCases\GetBadges;
use Domain\Bans\UseCases\GetActiveBan;
use Domain\Donations\UseCases\GetDonationTiers;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Resources\AccountResource;
use Entities\Resources\DonationPerkResource;
use Entities\Resources\GameBanV1Resource;
use Illuminate\Http\Request;
use Shared\PlayerLookup\Entities\PlayerIdentifier;

final class MinecraftAggregateController extends ApiController
{
    public function show(
        Request $request,
        string $uuid,
        GetActiveBan $getBan,
        GetBadges $getBadges,
        GetDonationTiers $getDonationTier,
    ) {
        $identifier = PlayerIdentifier::minecraftUUID($uuid);

        $ban = $getBan->execute(playerIdentifier: $identifier);
        $badges = $getBadges->execute(identifier: $identifier);

        try {
            $donationTiers = $getDonationTier->execute(uuid: $uuid);
        } catch (NotFoundException) {
            $donationTiers = [];
        }

        $account = $this->getLinkedAccount($uuid);

        return [
            'data' => [
                'account' => is_null($account) ? null : new AccountResource($account),
                'ban' => is_null($ban) ? null : new GameBanV1Resource($ban),
                'badges' => $badges,
                'donation_tiers' => DonationPerkResource::collection($donationTiers),
            ],
        ];
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
