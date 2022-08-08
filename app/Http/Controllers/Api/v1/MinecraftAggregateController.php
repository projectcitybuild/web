<?php

namespace App\Http\Controllers\Api\v1;

use App\Exceptions\Http\NotFoundException;
use App\Http\ApiController;
use Domain\Badges\UseCases\GetBadgesUseCase;
use Domain\Bans\UseCases\GetBanUseCase;
use Domain\Donations\UseCases\GetDonationTiersUseCase;
use Entities\Models\PlayerIdentifierType;
use Entities\Resources\DonationPerkResource;
use Entities\Resources\GameBanResource;
use Illuminate\Http\Request;
use Shared\PlayerLookup\Entities\PlayerIdentifier;

final class MinecraftAggregateController extends ApiController
{
    public function show(
        Request $request,
        string $uuid,
        GetBanUseCase $getBan,
        GetBadgesUseCase $getBadges,
        GetDonationTiersUseCase $getDonationTier,
    ) {
        $identifier = PlayerIdentifier::minecraftUUID($uuid);

        $ban = $getBan->execute(playerIdentifier: $identifier);
        $badges = $getBadges->execute(identifier: $identifier);

        try {
            $donationTiers = $getDonationTier->execute(uuid: $uuid);
        } catch (NotFoundException) {
            $donationTiers = [];
        }

        return [
            'data' => [
                'ban' => is_null($ban) ? null : new GameBanResource($ban),
                'badges' => $badges,
                'donation_tiers' => DonationPerkResource::collection($donationTiers),
            ],
        ];
    }
}
