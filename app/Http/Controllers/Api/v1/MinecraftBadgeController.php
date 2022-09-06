<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\APIController;
use Domain\Badges\UseCases\GetBadgesUseCase;
use Illuminate\Http\Request;
use Shared\PlayerLookup\Entities\PlayerIdentifier;

final class MinecraftBadgeController extends APIController
{
    public function show(
        Request $request,
        string $uuid,
        GetBadgesUseCase $getBadges,
    ) {
        $badges = $getBadges->execute(
            identifier: PlayerIdentifier::minecraftUUID($uuid)
        );

        return [
            'data' => $badges,
        ];
    }
}
