<?php

namespace App\Http\Controllers\Api\v1;

use App\Core\Domains\PlayerLookup\Data\PlayerIdentifier;
use App\Domains\Badges\UseCases\GetBadges;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

final class MinecraftBadgeController extends ApiController
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
