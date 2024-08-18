<?php

namespace App\Http\Controllers\API\v1;

use App\Core\Data\Exceptions\NotFoundException;
use App\Http\Controllers\APIController;
use App\Models\Account;
use App\Models\MinecraftPlayer;
use Domain\Badges\UseCases\GetBadges;
use Domain\Bans\UseCases\GetActiveIPBan;
use Domain\Bans\UseCases\GetActivePlayerBan;
use Domain\Donations\UseCases\GetDonationTiers;
use Entities\Resources\AccountResource;
use Entities\Resources\DonationPerkResource;
use Entities\Resources\GameIPBanResource;
use Entities\Resources\GamePlayerBanResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Shared\PlayerLookup\Entities\PlayerIdentifier;

final class MinecraftAggregateController extends APIController
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

        $uuid = str_replace(search: '-', replace: '', subject: $uuid);

        $identifier = PlayerIdentifier::minecraftUUID($uuid);

        $ban = $getBan->execute(playerIdentifier: $identifier);
        $badges = $getBadges->execute(identifier: $identifier);

        try {
            $donationTiers = $getDonationTier->execute(uuid: $uuid);
        } catch (NotFoundException) {
            $donationTiers = [];
        }

        $account = $this->getLinkedAccount($uuid);

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
