<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\ApiController;
use Domain\Badges\UseCases\GetBadgesUseCase;
use Illuminate\Http\Request;
use Shared\PlayerLookup\Entities\PlayerIdentifier;

final class MinecraftBadgeController extends ApiController
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
