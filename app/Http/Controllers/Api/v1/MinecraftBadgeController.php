<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\ApiController;
use Entities\Models\Eloquent\Badge;
use Illuminate\Http\Request;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\PlayerLookup;

final class MinecraftBadgeController extends ApiController
{
    public function show(
        Request $request,
        string $uuid,
        PlayerLookup $playerLookup,
    ) {
        $account = $playerLookup
            ->find(identifier: PlayerIdentifier::minecraftUUID($uuid))
            ?->getLinkedAccount();

        if ($account === null) {
            return ['data' => []];
        }

        $badges = $account->badges;
        $accountAgeInYears = $account->created_at->diffInYears(now());

        if ($accountAgeInYears >= 3) {
            $badge = new Badge();
            $badge->display_name = $accountAgeInYears.' years on PCB';
            $badge->unicode_icon = '⌚';
            $badges->add($badge);
        }

        return [
            'data' => $badges,
        ];
    }
}
