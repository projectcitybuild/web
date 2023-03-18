<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\APIController;
use Domain\Badges\UseCases\GetBadges;
use Illuminate\Http\Request;
use Shared\PlayerLookup\Entities\PlayerIdentifier;

final class MinecraftBadgeController extends APIController
{
    public function show(
        Request $request,
        string $uuid,
        GetBadges $getBadges,
    ) {
        $badges = $getBadges->execute(
            identifier: PlayerIdentifier::minecraftUUID($uuid)
        );

        return [
            'data' => $badges,
        ];
    }
}